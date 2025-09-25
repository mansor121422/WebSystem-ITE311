<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'name',
        'email', 
        'password',
        'role',
        'created_at',
        'updated_at'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // NO VALIDATION - Just save everything
    protected $skipValidation = true;
    protected $cleanValidationRules = false;

    // NO CALLBACKS - Let controller handle everything
    protected $beforeInsert = [];
    protected $beforeUpdate = [];

    /**
     * Find user by email
     */
    public function findByEmail(string $email)
    {
        return $this->where('email', $email)->first();
    }

    /**
     * Find user by ID
     */
    public function findById(int $id)
    {
        return $this->find($id);
    }

    /**
     * Get all users
     */
    public function getAllUsers()
    {
        return $this->findAll();
    }
}
