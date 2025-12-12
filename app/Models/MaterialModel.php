<?php

namespace App\Models;

use CodeIgniter\Model;

class MaterialModel extends Model
{
    protected $table = 'materials';
    protected $primaryKey = 'id';
    protected $allowedFields = ['course_id', 'teacher_id', 'file_name', 'file_path', 'created_at'];
    protected $useTimestamps = false;
    protected $createdField = 'created_at';

    /**
     * Insert a new material record
     * 
     * @param array $data
     * @return int|false The insert ID on success, false on failure
     */
    public function insertMaterial($data)
    {
        return $this->insert($data);
    }

    /**
     * Get all materials for a specific course
     * 
     * @param int $course_id
     * @param int|null $teacher_id If provided, only return materials uploaded by this teacher
     * @return array
     */
    public function getMaterialsByCourse($course_id, $teacher_id = null)
    {
        $query = $this->where('course_id', $course_id);
        
        // If teacher_id is provided, filter by teacher
        if ($teacher_id !== null) {
            $query->where('teacher_id', $teacher_id);
        }
        
        return $query->orderBy('created_at', 'DESC')
                    ->findAll();
    }
}

