<?php

namespace App\Models;

use CodeIgniter\Model;

class BusinessModel extends Model
{
    protected $table = 'businesses';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = ['name', 'owner_id', 'description', 'address', 'phone', 'email'];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Validation
    protected $validationRules = [
        'name' => 'required|min_length[2]',
        'owner_id' => 'required|integer',
        'description' => 'permit_empty',
        'address' => 'permit_empty',
        'phone' => 'permit_empty',
        'email' => 'permit_empty|valid_email',
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    // Relationships
    public function getOwner()
    {
        return $this->belongsTo(UserModel::class, 'owner_id');
    }
}