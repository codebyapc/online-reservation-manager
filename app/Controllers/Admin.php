<?php

namespace App\Controllers;

use App\Models\ReservationModel;
use App\Models\BusinessModel;
use App\Models\UserModel;
use CodeIgniter\Controller;

class Admin extends Controller
{
    protected $reservationModel;
    protected $businessModel;
    protected $userModel;

    public function __construct()
    {
        $this->reservationModel = new ReservationModel();
        $this->businessModel = new BusinessModel();
        $this->userModel = new UserModel();
    }

    public function dashboard()
    {
        if (!session()->get('is_logged_in') || session()->get('user_role') !== 'admin') {
            return redirect()->to('/auth/login');
        }

        $reservations = $this->reservationModel->findAll();
        $businesses = $this->businessModel->findAll();
        $users = $this->userModel->findAll();

        return view('admin/dashboard', [
            'reservations' => $reservations,
            'businesses' => $businesses,
            'users' => $users,
        ]);
    }

    public function reservations()
    {
        if (!session()->get('is_logged_in') || session()->get('user_role') !== 'admin') {
            return redirect()->to('/auth/login');
        }

        $reservations = $this->reservationModel->findAll();
        return view('admin/reservations', ['reservations' => $reservations]);
    }

    public function updateReservationStatus($id)
    {
        if (!session()->get('is_logged_in') || session()->get('user_role') !== 'admin') {
            return redirect()->to('/auth/login');
        }

        $status = $this->request->getPost('status');
        if (!in_array($status, ['pending', 'approved', 'rejected', 'cancelled'])) {
            return redirect()->back()->with('error', 'Invalid status');
        }

        if ($this->reservationModel->update($id, ['status' => $status])) {
            return redirect()->back()->with('success', 'Reservation status updated');
        }

        return redirect()->back()->with('error', 'Failed to update status');
    }

    public function businesses()
    {
        if (!session()->get('is_logged_in') || session()->get('user_role') !== 'admin') {
            return redirect()->to('/auth/login');
        }

        $businesses = $this->businessModel->findAll();
        return view('admin/businesses', ['businesses' => $businesses]);
    }

    public function createBusiness()
    {
        if (!session()->get('is_logged_in') || session()->get('user_role') !== 'admin') {
            return redirect()->to('/auth/login');
        }

        if ($this->request->getMethod() === 'post') {
            return $this->processCreateBusiness();
        }

        $users = $this->userModel->where('role', 'admin')->findAll();
        return view('admin/create_business', ['users' => $users]);
    }

    public function users()
    {
        if (!session()->get('is_logged_in') || session()->get('user_role') !== 'admin') {
            return redirect()->to('/auth/login');
        }

        $users = $this->userModel->findAll();
        return view('admin/users', ['users' => $users]);
    }

    private function processCreateBusiness()
    {
        $rules = [
            'name' => 'required|min_length[2]',
            'owner_id' => 'required|integer',
            'description' => 'permit_empty',
            'address' => 'permit_empty',
            'phone' => 'permit_empty',
            'email' => 'permit_empty|valid_email',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'name' => $this->request->getPost('name'),
            'owner_id' => $this->request->getPost('owner_id'),
            'description' => $this->request->getPost('description'),
            'address' => $this->request->getPost('address'),
            'phone' => $this->request->getPost('phone'),
            'email' => $this->request->getPost('email'),
        ];

        if ($this->businessModel->insert($data)) {
            return redirect()->to('/admin/businesses')->with('success', 'Business created successfully');
        }

        return redirect()->back()->withInput()->with('error', 'Failed to create business');
    }
}