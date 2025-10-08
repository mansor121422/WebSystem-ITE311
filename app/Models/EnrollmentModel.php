<?php

namespace App\Models;

use CodeIgniter\Model;

class EnrollmentModel extends Model
{
    protected $table = 'enrollments';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'user_id',
        'course_id',
        'enrollment_date',
        'status',
        'progress',
        'created_at',
        'updated_at'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Validation rules
    protected $validationRules = [
        'user_id' => 'required|integer',
        'course_id' => 'required|integer',
        'enrollment_date' => 'required|valid_date',
        'status' => 'permit_empty|in_list[active,completed,dropped,suspended]',
        'progress' => 'permit_empty|decimal|less_than_equal_to[100.00]'
    ];

    protected $validationMessages = [
        'user_id' => [
            'required' => 'User ID is required',
            'integer' => 'User ID must be a valid integer'
        ],
        'course_id' => [
            'required' => 'Course ID is required',
            'integer' => 'Course ID must be a valid integer'
        ],
        'enrollment_date' => [
            'required' => 'Enrollment date is required',
            'valid_date' => 'Enrollment date must be a valid date'
        ],
        'status' => [
            'in_list' => 'Status must be one of: active, completed, dropped, suspended'
        ],
        'progress' => [
            'decimal' => 'Progress must be a decimal number',
            'less_than_equal_to' => 'Progress cannot exceed 100.00'
        ]
    ];

    /**
     * Enroll a user in a course
     * 
     * @param array $data Enrollment data
     * @return int|false Returns the inserted ID on success, false on failure
     */
    public function enrollUser($data)
    {
        // Set default values if not provided
        $enrollmentData = [
            'user_id' => $data['user_id'],
            'course_id' => $data['course_id'],
            'enrollment_date' => $data['enrollment_date'] ?? date('Y-m-d H:i:s'),
            'status' => $data['status'] ?? 'active',
            'progress' => $data['progress'] ?? 0.00
        ];

        // Check if user is already enrolled
        if ($this->isAlreadyEnrolled($enrollmentData['user_id'], $enrollmentData['course_id'])) {
            return false; // User already enrolled
        }

        try {
            $result = $this->insert($enrollmentData);
            return $result ? $this->getInsertID() : false;
        } catch (\Exception $e) {
            log_message('error', 'Enrollment failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get all courses a user is enrolled in
     * 
     * @param int $user_id User ID
     * @param string $status Optional status filter (active, completed, dropped, suspended)
     * @return array Array of enrollment records with course details
     */
    public function getUserEnrollments($user_id, $status = null)
    {
        $builder = $this->db->table('enrollments e');
        $builder->select('e.*, c.title as course_title, c.description as course_description, c.instructor_id');
        $builder->join('courses c', 'c.id = e.course_id', 'left');
        $builder->where('e.user_id', $user_id);

        if ($status !== null) {
            $builder->where('e.status', $status);
        }

        $builder->orderBy('e.enrollment_date', 'DESC');
        
        return $builder->get()->getResultArray();
    }

    /**
     * Check if a user is already enrolled in a specific course
     * 
     * @param int $user_id User ID
     * @param int $course_id Course ID
     * @return bool True if already enrolled, false otherwise
     */
    public function isAlreadyEnrolled($user_id, $course_id)
    {
        $enrollment = $this->where('user_id', $user_id)
                          ->where('course_id', $course_id)
                          ->first();

        return $enrollment !== null;
    }

    /**
     * Get enrollment details by user and course
     * 
     * @param int $user_id User ID
     * @param int $course_id Course ID
     * @return array|null Enrollment record or null if not found
     */
    public function getEnrollment($user_id, $course_id)
    {
        return $this->where('user_id', $user_id)
                   ->where('course_id', $course_id)
                   ->first();
    }

    /**
     * Update enrollment status
     * 
     * @param int $user_id User ID
     * @param int $course_id Course ID
     * @param string $status New status
     * @return bool True on success, false on failure
     */
    public function updateEnrollmentStatus($user_id, $course_id, $status)
    {
        $allowedStatuses = ['active', 'completed', 'dropped', 'suspended'];
        
        if (!in_array($status, $allowedStatuses)) {
            return false;
        }

        return $this->where('user_id', $user_id)
                   ->where('course_id', $course_id)
                   ->set('status', $status)
                   ->update();
    }

    /**
     * Update enrollment progress
     * 
     * @param int $user_id User ID
     * @param int $course_id Course ID
     * @param float $progress Progress percentage (0.00 to 100.00)
     * @return bool True on success, false on failure
     */
    public function updateEnrollmentProgress($user_id, $course_id, $progress)
    {
        if ($progress < 0 || $progress > 100) {
            return false;
        }

        return $this->where('user_id', $user_id)
                   ->where('course_id', $course_id)
                   ->set('progress', $progress)
                   ->update();
    }

    /**
     * Get all enrollments for a specific course
     * 
     * @param int $course_id Course ID
     * @param string $status Optional status filter
     * @return array Array of enrollment records with user details
     */
    public function getCourseEnrollments($course_id, $status = null)
    {
        $builder = $this->db->table('enrollments e');
        $builder->select('e.*, u.name as user_name, u.email as user_email');
        $builder->join('users u', 'u.id = e.user_id', 'left');
        $builder->where('e.course_id', $course_id);

        if ($status !== null) {
            $builder->where('e.status', $status);
        }

        $builder->orderBy('e.enrollment_date', 'DESC');
        
        return $builder->get()->getResultArray();
    }

    /**
     * Get enrollment statistics
     * 
     * @param int $user_id Optional user ID for user-specific stats
     * @return array Statistics array
     */
    public function getEnrollmentStats($user_id = null)
    {
        $builder = $this->db->table('enrollments');
        
        if ($user_id !== null) {
            $builder->where('user_id', $user_id);
        }

        $stats = [
            'total' => $builder->countAllResults(false),
            'active' => $builder->where('status', 'active')->countAllResults(false),
            'completed' => $builder->where('status', 'completed')->countAllResults(false),
            'dropped' => $builder->where('status', 'dropped')->countAllResults(false),
            'suspended' => $builder->where('status', 'suspended')->countAllResults(false)
        ];

        return $stats;
    }
}
