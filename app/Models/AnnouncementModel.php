<?php

namespace App\Models;

use CodeIgniter\Model;

class AnnouncementModel extends Model
{
    protected $table = 'announcements';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'title',
        'content',
        'posted_by',
        'date_posted',
        'created_at',
        'updated_at'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Validation
    protected $validationRules = [
        'title' => 'required|min_length[3]|max_length[255]',
        'content' => 'required|min_length[10]',
        'posted_by' => 'required|integer',
        'date_posted' => 'required'
    ];

    protected $validationMessages = [
        'title' => [
            'required' => 'Title is required',
            'min_length' => 'Title must be at least 3 characters',
            'max_length' => 'Title cannot exceed 255 characters'
        ],
        'content' => [
            'required' => 'Content is required',
            'min_length' => 'Content must be at least 10 characters'
        ]
    ];

    protected $skipValidation = false;

    /**
     * Get all announcements with user information
     */
    public function getAnnouncementsWithUser()
    {
        return $this->select('announcements.*, users.name as author_name')
                    ->join('users', 'users.id = announcements.posted_by')
                    ->orderBy('announcements.date_posted', 'DESC')
                    ->findAll();
    }

    /**
     * Get recent announcements (last 5)
     */
    public function getRecentAnnouncements($limit = 5)
    {
        return $this->orderBy('date_posted', 'DESC')
                    ->limit($limit)
                    ->findAll();
    }

    /**
     * Get announcement by ID with user information
     */
    public function getAnnouncementById($id)
    {
        return $this->select('announcements.*, users.name as author_name')
                    ->join('users', 'users.id = announcements.posted_by')
                    ->where('announcements.id', $id)
                    ->first();
    }
}

