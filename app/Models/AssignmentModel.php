<?php

namespace App\Models;

use CodeIgniter\Model;

class AssignmentModel extends Model
{
    protected $table = 'assignments';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'course_id',
        'title',
        'description',
        'instructions',
        'due_date',
        'max_score',
        'created_by',
        'is_published',
        'created_at',
        'updated_at'
    ];

    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'course_id' => 'required|integer',
        'title' => 'required|min_length[3]|max_length[255]',
        'description' => 'permit_empty',
        'max_score' => 'permit_empty|decimal',
        'created_by' => 'required|integer',
    ];

    protected $skipValidation = false;

    /**
     * Get assignments for a specific course
     */
    public function getAssignmentsByCourse($courseId)
    {
        return $this->where('course_id', $courseId)
                    ->where('is_published', true)
                    ->orderBy('created_at', 'DESC')
                    ->findAll();
    }

    /**
     * Get assignment with course and creator info
     */
    public function getAssignmentWithDetails($assignmentId)
    {
        return $this->select('assignments.*, courses.title as course_title, users.name as creator_name')
                    ->join('courses', 'courses.id = assignments.course_id')
                    ->join('users', 'users.id = assignments.created_by')
                    ->where('assignments.id', $assignmentId)
                    ->first();
    }

    /**
     * Get all assignments created by a teacher
     */
    public function getAssignmentsByTeacher($teacherId)
    {
        return $this->select('assignments.*, courses.title as course_title')
                    ->join('courses', 'courses.id = assignments.course_id')
                    ->where('assignments.created_by', $teacherId)
                    ->orderBy('assignments.created_at', 'DESC')
                    ->findAll();
    }
}

