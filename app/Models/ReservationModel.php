<?php

namespace App\Models;

use CodeIgniter\Model;

class ReservationModel extends Model
{
    protected $table = 'reservations';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = ['user_id', 'business_id', 'reservation_date', 'reservation_time', 'status', 'notes'];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Validation
    protected $validationRules = [
        'user_id' => 'required|integer',
        'business_id' => 'required|integer',
        'reservation_date' => 'required|valid_date[Y-m-d]',
        'reservation_time' => 'required|regex_match[/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/]',
        'status' => 'required|in_list[pending,approved,rejected,cancelled]',
        'notes' => 'permit_empty',
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    // Relationships
    public function getUser()
    {
        return $this->belongsTo(UserModel::class, 'user_id');
    }

    public function getBusiness()
    {
        return $this->belongsTo(BusinessModel::class, 'business_id');
    }

    // Custom methods
    public function getReservationsByBusiness($businessId, $date = null)
    {
        $builder = $this->where('business_id', $businessId);
        if ($date) {
            $builder->where('reservation_date', $date);
        }
        return $builder->findAll();
    }

    public function getReservationsByUser($userId)
    {
        return $this->where('user_id', $userId)->findAll();
    }

    public function checkAvailability($businessId, $date, $time)
    {
        return $this->where('business_id', $businessId)
                    ->where('reservation_date', $date)
                    ->where('reservation_time', $time)
                    ->where('status !=', 'cancelled')
                    ->first() === null;
    }
}