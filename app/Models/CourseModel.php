<?php

namespace App\Models;

use CodeIgniter\Model;

class CourseModel extends Model
{
    protected $table            = 'courses';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'title', 
        'description', 
        'instructor_id',
        'course_code',
        'school_year',
        'semester',
        'schedule_day',
        'schedule_time_start',
        'schedule_time_end',
        'max_students',
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
        'title' => 'required|min_length[3]|max_length[150]',
        'description' => 'permit_empty|max_length[1000]'
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
     * Get all available courses
     */
    public function getAvailableCourses()
    {
        return $this->orderBy('title', 'ASC')->findAll();
    }

    /**
     * Get courses not enrolled by a specific user
     */
    public function getCoursesNotEnrolledByUser($user_id)
    {
        // Get all courses
        $allCourses = $this->findAll();
        
        // If no courses exist, return empty array
        if (empty($allCourses)) {
            return [];
        }
        
        // Get enrolled course IDs for this user
        $enrolledCourseIds = $this->db->table('enrollments')
                                    ->select('course_id')
                                    ->where('user_id', $user_id)
                                    ->get()
                                    ->getResultArray();
        
        $enrolledIds = array_column($enrolledCourseIds, 'course_id');
        
        // Filter out enrolled courses
        $availableCourses = array_filter($allCourses, function($course) use ($enrolledIds) {
            return !in_array($course['id'], $enrolledIds);
        });
        
        return array_values($availableCourses);
    }

    /**
     * Get course by ID with enrollment count
     */
    public function getCourseWithEnrollmentCount($course_id)
    {
        $enrollmentModel = new EnrollmentModel();
        $course = $this->find($course_id);
        
        if ($course) {
            $course['enrollment_count'] = $enrollmentModel->getCourseEnrollmentCount($course_id);
        }
        
        return $course;
    }

    /**
     * Get popular courses (most enrolled)
     */
    public function getPopularCourses($limit = 5)
    {
        return $this->select('courses.*, COUNT(enrollments.id) as enrollment_count')
                    ->join('enrollments', 'enrollments.course_id = courses.id', 'left')
                    ->groupBy('courses.id')
                    ->orderBy('enrollment_count', 'DESC')
                    ->limit($limit)
                    ->findAll();
    }

    /**
     * Check for duplicate course code and title combination
     * Since course code and title are NOT unique, we need strict validation
     */
    public function checkDuplicateCourse($courseCode, $title, $schoolYear, $semester, $excludeId = null)
    {
        $query = $this->where('course_code', $courseCode)
                     ->where('title', $title)
                     ->where('school_year', $schoolYear)
                     ->where('semester', $semester);
        
        if ($excludeId) {
            $query->where('id !=', $excludeId);
        }
        
        return $query->first();
    }

    /**
     * Check for schedule conflicts with other courses
     * Handles both single day and multiple days (comma-separated)
     */
    public function checkScheduleConflict($scheduleDay, $timeStart, $timeEnd, $schoolYear, $semester, $excludeId = null)
    {
        // Check if schedule_day contains the specified day (handles comma-separated values)
        $query = $this->where('school_year', $schoolYear)
                     ->where('semester', $semester)
                     ->groupStart()
                         ->like('schedule_day', $scheduleDay)
                         ->orWhere('schedule_day', $scheduleDay)
                     ->groupEnd()
                     ->groupStart()
                         ->groupStart()
                             ->where('schedule_time_start <=', $timeStart)
                             ->where('schedule_time_end >', $timeStart)
                         ->groupEnd()
                         ->orGroupStart()
                             ->where('schedule_time_start <', $timeEnd)
                             ->where('schedule_time_end >=', $timeEnd)
                         ->groupEnd()
                         ->orGroupStart()
                             ->where('schedule_time_start >=', $timeStart)
                             ->where('schedule_time_end <=', $timeEnd)
                         ->groupEnd()
                     ->groupEnd();
        
        if ($excludeId) {
            $query->where('id !=', $excludeId);
        }
        
        return $query->findAll();
    }

    /**
     * Check for schedule conflicts for a specific teacher
     * This ensures teachers don't have overlapping class schedules
     * 
     * @param int $teacherId The instructor ID to check
     * @param string|array $scheduleDays Day(s) to check (can be comma-separated string or array)
     * @param string $timeStart Start time (HH:MM format)
     * @param string $timeEnd End time (HH:MM format)
     * @param string $schoolYear School year
     * @param string $semester Semester
     * @param int|null $excludeId Course ID to exclude from check (for updates)
     * @return array Array of conflicting courses
     */
    public function checkTeacherScheduleConflict($teacherId, $scheduleDays, $timeStart, $timeEnd, $schoolYear, $semester, $excludeId = null)
    {
        if (empty($teacherId) || empty($scheduleDays) || empty($timeStart) || empty($timeEnd) || empty($schoolYear) || empty($semester)) {
            return [];
        }

        // Convert scheduleDays to array if it's a comma-separated string
        if (is_string($scheduleDays)) {
            $daysArray = array_map('trim', explode(',', $scheduleDays));
        } else {
            $daysArray = $scheduleDays;
        }

        $conflicts = [];

        // Check each day for conflicts
        foreach ($daysArray as $day) {
            $day = trim($day);
            if (empty($day)) {
                continue;
            }

            // Find all courses assigned to this teacher in the same school year and semester
            $teacherCourses = $this->where('instructor_id', $teacherId)
                                 ->where('school_year', $schoolYear)
                                 ->where('semester', $semester)
                                 ->where('schedule_day IS NOT NULL')
                                 ->where('schedule_time_start IS NOT NULL')
                                 ->where('schedule_time_end IS NOT NULL');

            if ($excludeId) {
                $teacherCourses->where('id !=', $excludeId);
            }

            $teacherCourses = $teacherCourses->findAll();

            // Check each of the teacher's existing courses for time conflicts on this day
            foreach ($teacherCourses as $course) {
                $courseDays = array_map('trim', explode(',', $course['schedule_day']));
                
                // Check if this course is scheduled on the same day
                if (in_array($day, $courseDays)) {
                    $courseStart = $course['schedule_time_start'];
                    $courseEnd = $course['schedule_time_end'];

                    // Check for time overlap
                    // Conflict occurs if:
                    // 1. New start time is between existing start and end
                    // 2. New end time is between existing start and end
                    // 3. New time completely encompasses existing time
                    // 4. Existing time completely encompasses new time
                    if (
                        ($timeStart >= $courseStart && $timeStart < $courseEnd) ||
                        ($timeEnd > $courseStart && $timeEnd <= $courseEnd) ||
                        ($timeStart <= $courseStart && $timeEnd >= $courseEnd) ||
                        ($timeStart >= $courseStart && $timeEnd <= $courseEnd)
                    ) {
                        $conflicts[] = [
                            'course_id' => $course['id'],
                            'course_title' => $course['title'],
                            'day' => $day,
                            'existing_time' => $courseStart . '-' . $courseEnd,
                            'new_time' => $timeStart . '-' . $timeEnd
                        ];
                    }
                }
            }
        }

        return $conflicts;
    }

    /**
     * Get courses by school year and semester
     */
    public function getCoursesBySemester($schoolYear, $semester)
    {
        return $this->where('school_year', $schoolYear)
                   ->where('semester', $semester)
                   ->findAll();
    }
}
