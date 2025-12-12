<?php

namespace App\Models;

use CodeIgniter\Model;

class QuizSubmissionModel extends Model
{
    protected $table = 'submissions';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'user_id',
        'quiz_id',
        'submission_data',
        'score',
        'total_questions',
        'correct_answers',
        'time_taken',
        'attempt_number',
        'status',
        'started_at',
        'submitted_at',
        'created_at',
        'updated_at'
    ];

    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'user_id' => 'required|integer',
        'quiz_id' => 'required|integer',
    ];

    protected $skipValidation = false;

    /**
     * Get submission for a user and quiz
     */
    public function getSubmission($quizId, $userId)
    {
        return $this->where('quiz_id', $quizId)
                    ->where('user_id', $userId)
                    ->orderBy('attempt_number', 'DESC')
                    ->first();
    }

    /**
     * Get all submissions for a quiz
     */
    public function getSubmissionsByQuiz($quizId)
    {
        return $this->select('submissions.*, users.name as student_name, users.email as student_email')
                    ->join('users', 'users.id = submissions.user_id')
                    ->where('submissions.quiz_id', $quizId)
                    ->orderBy('submissions.submitted_at', 'DESC')
                    ->findAll();
    }

    /**
     * Get all submissions for a user
     */
    public function getSubmissionsByUser($userId)
    {
        return $this->select('submissions.*, quizzes.title as quiz_title, courses.title as course_title')
                    ->join('quizzes', 'quizzes.id = submissions.quiz_id')
                    ->join('courses', 'courses.id = quizzes.course_id')
                    ->where('submissions.user_id', $userId)
                    ->orderBy('submissions.submitted_at', 'DESC')
                    ->findAll();
    }

    /**
     * Get attempt count for a user and quiz
     */
    public function getAttemptCount($quizId, $userId)
    {
        return $this->where('quiz_id', $quizId)
                    ->where('user_id', $userId)
                    ->where('status', 'completed')
                    ->countAllResults();
    }

    /**
     * Get next attempt number for a user and quiz
     */
    public function getNextAttemptNumber($quizId, $userId)
    {
        $lastAttempt = $this->where('quiz_id', $quizId)
                           ->where('user_id', $userId)
                           ->orderBy('attempt_number', 'DESC')
                           ->first();
        
        return $lastAttempt ? ($lastAttempt['attempt_number'] + 1) : 1;
    }
}


