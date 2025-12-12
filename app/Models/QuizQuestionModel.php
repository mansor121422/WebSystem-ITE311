<?php

namespace App\Models;

use CodeIgniter\Model;

class QuizQuestionModel extends Model
{
    protected $table = 'quiz_questions';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'quiz_id',
        'question_text',
        'question_type',
        'options',
        'correct_answer',
        'points',
        'order_index',
        'created_at',
        'updated_at'
    ];

    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'quiz_id' => 'required|integer',
        'question_text' => 'required',
        'question_type' => 'required|in_list[multiple_choice,true_false,short_answer]',
        'correct_answer' => 'required',
    ];

    protected $skipValidation = false;

    /**
     * Get all questions for a quiz
     */
    public function getQuestionsByQuiz($quizId, $randomize = false)
    {
        $query = $this->where('quiz_id', $quizId)
                     ->orderBy('order_index', 'ASC');
        
        if ($randomize) {
            $query->orderBy('RAND()', '', false);
        }
        
        return $query->findAll();
    }

    /**
     * Get question with options decoded
     */
    public function getQuestionWithOptions($questionId)
    {
        $question = $this->find($questionId);
        if ($question && !empty($question['options'])) {
            $question['options'] = json_decode($question['options'], true);
        }
        return $question;
    }
}


