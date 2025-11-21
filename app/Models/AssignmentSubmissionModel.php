<?php

namespace App\Models;

use CodeIgniter\Model;

class AssignmentSubmissionModel extends Model
{
    protected $table = 'assignment_submissions';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'assignment_id',
        'student_id',
        'submission_text',
        'file_path',
        'file_name',
        'score',
        'feedback',
        'status',
        'submitted_at',
        'graded_at',
        'graded_by',
        'created_at',
        'updated_at'
    ];

    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'assignment_id' => 'required|integer',
        'student_id' => 'required|integer',
    ];

    protected $skipValidation = false;

    /**
     * Get submission for a specific assignment and student
     */
    public function getSubmission($assignmentId, $studentId)
    {
        return $this->where('assignment_id', $assignmentId)
                    ->where('student_id', $studentId)
                    ->first();
    }

    /**
     * Get all submissions for an assignment
     */
    public function getSubmissionsByAssignment($assignmentId)
    {
        return $this->select('assignment_submissions.*, users.name as student_name, users.email as student_email')
                    ->join('users', 'users.id = assignment_submissions.student_id')
                    ->where('assignment_submissions.assignment_id', $assignmentId)
                    ->orderBy('assignment_submissions.submitted_at', 'DESC')
                    ->findAll();
    }

    /**
     * Get all submissions by a student
     */
    public function getSubmissionsByStudent($studentId)
    {
        return $this->select('assignment_submissions.*, assignments.title as assignment_title, assignments.course_id, courses.title as course_title')
                    ->join('assignments', 'assignments.id = assignment_submissions.assignment_id')
                    ->join('courses', 'courses.id = assignments.course_id')
                    ->where('assignment_submissions.student_id', $studentId)
                    ->orderBy('assignment_submissions.submitted_at', 'DESC')
                    ->findAll();
    }

    /**
     * Check if student has already submitted
     */
    public function hasSubmitted($assignmentId, $studentId)
    {
        return $this->where('assignment_id', $assignmentId)
                    ->where('student_id', $studentId)
                    ->countAllResults() > 0;
    }
}

