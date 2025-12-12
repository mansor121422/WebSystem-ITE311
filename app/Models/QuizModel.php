<?php

namespace App\Models;

use CodeIgniter\Model;

class QuizModel extends Model
{
    protected $table = 'quizzes';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'course_id',
        'lesson_id',
        'title',
        'description',
        'instructions',
        'time_limit',
        'max_attempts',
        'passing_score',
        'is_published',
        'show_correct_answers',
        'randomize_questions',
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
    ];

    protected $skipValidation = false;

    /**
     * Get quizzes for a specific course
     */
    public function getQuizzesByCourse($courseId)
    {
        return $this->where('course_id', $courseId)
                    ->where('is_published', true)
                    ->orderBy('created_at', 'DESC')
                    ->findAll();
    }

    /**
     * Get quiz with course info
     */
    public function getQuizWithDetails($quizId)
    {
        return $this->select('quizzes.*, courses.title as course_title')
                    ->join('courses', 'courses.id = quizzes.course_id')
                    ->where('quizzes.id', $quizId)
                    ->first();
    }

    /**
     * Get all quizzes created by a teacher (for courses they teach)
     */
    public function getQuizzesByTeacher($teacherId)
    {
        return $this->select('quizzes.*, courses.title as course_title')
                    ->join('courses', 'courses.id = quizzes.course_id')
                    ->where('courses.instructor_id', $teacherId)
                    ->orderBy('quizzes.created_at', 'DESC')
                    ->findAll();
    }
}


