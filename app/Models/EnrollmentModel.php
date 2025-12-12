<?php

namespace App\Models;

use CodeIgniter\Model;

class EnrollmentModel extends Model
{
    protected $table            = 'enrollments';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'user_id', 
        'course_id', 
        'enrollment_date', 
        'semester_end_date', 
        'status', 
        'progress', 
        'school_year',
        'semester',
        'requested_by',
        'approved_by',
        'approved_at',
        'created_at', 
        'updated_at'
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [];
    protected array $castHandlers = [];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [
        'user_id' => 'required|integer',
        'course_id' => 'required|integer',
        'enrollment_date' => 'required|valid_date'
    ];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    /**
     * Enroll a user in a course
     */
    public function enrollUser($data)
    {
        // Set enrollment date if not provided
        if (!isset($data['enrollment_date'])) {
            $data['enrollment_date'] = date('Y-m-d H:i:s');
        }

        // Set semester end date (4 months from enrollment date)
        if (!isset($data['semester_end_date'])) {
            $enrollmentDate = new \DateTime($data['enrollment_date']);
            $enrollmentDate->modify('+4 months');
            $data['semester_end_date'] = $enrollmentDate->format('Y-m-d H:i:s');
        }

        return $this->insert($data);
    }

    /**
     * Get active enrollments (not expired)
     */
    public function getActiveEnrollments($user_id)
    {
        $now = date('Y-m-d H:i:s');
        return $this->where('user_id', $user_id)
                    ->where('status', 'active')
                    ->groupStart()
                        ->where('semester_end_date IS NULL')
                        ->orWhere('semester_end_date >', $now)
                    ->groupEnd()
                    ->findAll();
    }

    /**
     * Check if enrollment is expired
     */
    public function isEnrollmentExpired($enrollment)
    {
        if (empty($enrollment['semester_end_date'])) {
            return false; // No expiration date set
        }

        $now = new \DateTime();
        $endDate = new \DateTime($enrollment['semester_end_date']);
        
        return $now > $endDate;
    }

    /**
     * Expire old enrollments (mark as expired after 4 months)
     */
    public function expireOldEnrollments()
    {
        $now = date('Y-m-d H:i:s');
        return $this->where('status', 'active')
                    ->where('semester_end_date IS NOT NULL')
                    ->where('semester_end_date <=', $now)
                    ->set('status', 'expired')
                    ->update();
    }

    /**
     * Get all courses a user is enrolled in
     */
    public function getUserEnrollments($user_id)
    {
        return $this->select('enrollments.*, courses.title, courses.description')
                    ->join('courses', 'courses.id = enrollments.course_id')
                    ->where('enrollments.user_id', $user_id)
                    ->orderBy('enrollments.enrollment_date', 'DESC')
                    ->findAll();
    }

    /**
     * Check if a user is already enrolled in a specific course
     */
    public function isAlreadyEnrolled($user_id, $course_id)
    {
        $enrollment = $this->where('user_id', $user_id)
                          ->where('course_id', $course_id)
                          ->whereIn('status', ['active', 'pending'])
                          ->first();
        
        return $enrollment !== null;
    }

    /**
     * Get pending enrollments for a user
     */
    public function getPendingEnrollments($user_id)
    {
        return $this->where('user_id', $user_id)
                    ->where('status', 'pending')
                    ->orderBy('enrollment_date', 'DESC')
                    ->findAll();
    }

    /**
     * Get enrollment count for a course
     */
    public function getCourseEnrollmentCount($course_id)
    {
        return $this->where('course_id', $course_id)->countAllResults();
    }

    /**
     * Get all enrollments with user and course details
     */
    public function getEnrollmentsWithDetails()
    {
        return $this->select('enrollments.*, users.name as user_name, users.email, courses.title as course_title')
                    ->join('users', 'users.id = enrollments.user_id')
                    ->join('courses', 'courses.id = enrollments.course_id')
                    ->orderBy('enrollments.enrollment_date', 'DESC')
                    ->findAll();
    }

    /**
     * Check for schedule conflicts for a student
     * Validates that the student doesn't have overlapping class schedules
     * Handles multiple schedule days (comma-separated)
     */
    public function checkStudentScheduleConflict($studentId, $courseId, $schoolYear, $semester)
    {
        // Get the course schedule
        $course = $this->db->table('courses')
                          ->where('id', $courseId)
                          ->get()
                          ->getRowArray();
        
        if (!$course || empty($course['schedule_day']) || empty($course['schedule_time_start']) || empty($course['schedule_time_end'])) {
            // No schedule conflict if course has no schedule
            return false;
        }

        // Get all active enrollments for this student in the same school year and semester
        $enrollments = $this->select('enrollments.*, courses.schedule_day, courses.schedule_time_start, courses.schedule_time_end')
                           ->join('courses', 'courses.id = enrollments.course_id')
                           ->where('enrollments.user_id', $studentId)
                           ->where('enrollments.school_year', $schoolYear)
                           ->where('enrollments.semester', $semester)
                           ->whereIn('enrollments.status', ['active', 'pending'])
                           ->where('enrollments.course_id !=', $courseId)
                           ->findAll();

        // Parse course schedule days (handle comma-separated)
        $courseDays = array_map('trim', explode(',', $course['schedule_day']));
        $courseStart = strtotime($course['schedule_time_start']);
        $courseEnd = strtotime($course['schedule_time_end']);

        // Check for time conflicts
        foreach ($enrollments as $enrollment) {
            if (empty($enrollment['schedule_day']) || empty($enrollment['schedule_time_start']) || empty($enrollment['schedule_time_end'])) {
                continue;
            }

            // Parse enrollment schedule days (handle comma-separated)
            $enrollmentDays = array_map('trim', explode(',', $enrollment['schedule_day']));
            
            // Check if any days overlap
            $commonDays = array_intersect($courseDays, $enrollmentDays);
            if (!empty($commonDays)) {
                // Check for time overlap
                $enrollmentStart = strtotime($enrollment['schedule_time_start']);
                $enrollmentEnd = strtotime($enrollment['schedule_time_end']);

                // Check if times overlap
                if (($courseStart < $enrollmentEnd) && ($courseEnd > $enrollmentStart)) {
                    $conflictDay = implode(', ', $commonDays);
                    return [
                        'conflict' => true,
                        'conflicting_course' => $enrollment,
                        'message' => "Schedule conflict on {$conflictDay} {$enrollment['schedule_time_start']}-{$enrollment['schedule_time_end']}"
                    ];
                }
            }
        }

        return false;
    }

    /**
     * Check if student is already enrolled in same course for same school year and semester
     */
    public function checkDuplicateEnrollment($studentId, $courseId, $schoolYear, $semester)
    {
        return $this->where('user_id', $studentId)
                   ->where('course_id', $courseId)
                   ->where('school_year', $schoolYear)
                   ->where('semester', $semester)
                   ->whereIn('status', ['active', 'pending'])
                   ->first();
    }

    /**
     * Get pending enrollment requests for teacher approval
     */
    public function getPendingRequestsForTeacher($teacherId)
    {
        // Use raw query to handle NULL instructor_id cases more reliably
        // Show requests where: course instructor matches teacher OR course has no instructor (unassigned courses)
        // For now, show ALL pending requests to teachers (can be filtered later if needed)
        $db = \Config\Database::connect();
        $query = $db->query("
            SELECT e.*, 
                   u.name as student_name, 
                   u.email as student_email, 
                   COALESCE(c.title, CONCAT('Course #', e.course_id)) as course_title, 
                   c.course_code, 
                   COALESCE(e.school_year, c.school_year) as school_year, 
                   COALESCE(e.semester, c.semester) as semester, 
                   c.instructor_id,
                   t.name as teacher_name,
                   c.schedule_day,
                   c.schedule_time_start,
                   c.schedule_time_end
            FROM enrollments e
            LEFT JOIN users u ON u.id = e.user_id
            LEFT JOIN courses c ON c.id = e.course_id
            LEFT JOIN users t ON t.id = c.instructor_id
            WHERE e.status = 'pending'
            ORDER BY e.enrollment_date DESC
        ");
        
        return $query->getResultArray();
    }

    /**
     * Get enrollment requests for admin
     * Admin should see ALL pending enrollment requests
     */
    public function getPendingRequestsForAdmin()
    {
        // Use raw query for consistency and to handle all edge cases
        // Admin sees ALL pending requests regardless of instructor
        $db = \Config\Database::connect();
        $query = $db->query("
            SELECT e.*, 
                   u.name as student_name, 
                   u.email as student_email, 
                   COALESCE(c.title, CONCAT('Course #', e.course_id)) as course_title, 
                   c.course_code, 
                   COALESCE(e.school_year, c.school_year) as school_year, 
                   COALESCE(e.semester, c.semester) as semester, 
                   t.name as teacher_name,
                   c.instructor_id,
                   c.schedule_day,
                   c.schedule_time_start,
                   c.schedule_time_end
            FROM enrollments e
            LEFT JOIN users u ON u.id = e.user_id
            LEFT JOIN courses c ON c.id = e.course_id
            LEFT JOIN users t ON t.id = c.instructor_id
            WHERE e.status = 'pending'
            ORDER BY e.enrollment_date DESC
        ");
        
        return $query->getResultArray();
    }
}
