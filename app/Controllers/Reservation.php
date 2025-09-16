<?php

namespace App\Controllers;

use App\Models\ReservationModel;
use App\Models\BusinessModel;
use CodeIgniter\Controller;

class Reservation extends Controller
{
    protected $reservationModel;
    protected $businessModel;

    public function __construct()
    {
        $this->reservationModel = new ReservationModel();
        $this->businessModel = new BusinessModel();
    }

    public function index()
    {
        if (!session()->get('is_logged_in')) {
            return redirect()->to('/auth/login');
        }

        $userId = session()->get('user_id');
        $reservations = $this->reservationModel->getReservationsByUser($userId);

        return view('reservations/index', ['reservations' => $reservations]);
    }

    public function create()
    {
        if (!session()->get('is_logged_in')) {
            return redirect()->to('/auth/login');
        }

        if ($this->request->getMethod() === 'post') {
            return $this->processCreate();
        }

        $businesses = $this->businessModel->findAll();
        return view('reservations/create', ['businesses' => $businesses]);
    }

    public function edit($id)
    {
        if (!session()->get('is_logged_in')) {
            return redirect()->to('/auth/login');
        }

        $reservation = $this->reservationModel->find($id);
        if (!$reservation || $reservation['user_id'] != session()->get('user_id')) {
            return redirect()->to('/reservations')->with('error', 'Reservation not found');
        }

        if ($this->request->getMethod() === 'post') {
            return $this->processUpdate($id);
        }

        $businesses = $this->businessModel->findAll();
        return view('reservations/edit', ['reservation' => $reservation, 'businesses' => $businesses]);
    }

    public function delete($id)
    {
        if (!session()->get('is_logged_in')) {
            return redirect()->to('/auth/login');
        }

        $reservation = $this->reservationModel->find($id);
        if (!$reservation || $reservation['user_id'] != session()->get('user_id')) {
            return redirect()->to('/reservations')->with('error', 'Reservation not found');
        }

        if ($this->reservationModel->delete($id)) {
            return redirect()->to('/reservations')->with('success', 'Reservation cancelled successfully');
        }

        return redirect()->to('/reservations')->with('error', 'Failed to cancel reservation');
    }

    public function calendar($businessId = null)
    {
        if (!session()->get('is_logged_in')) {
            return redirect()->to('/auth/login');
        }

        $businesses = $this->businessModel->findAll();
        $selectedBusiness = $businessId ? $this->businessModel->find($businessId) : null;

        return view('reservations/calendar', [
            'businesses' => $businesses,
            'selectedBusiness' => $selectedBusiness
        ]);
    }

    public function apiGetReservations($businessId, $date)
    {
        if (!session()->get('is_logged_in')) {
            return $this->response->setJSON(['error' => 'Unauthorized'])->setStatusCode(401);
        }

        $reservations = $this->reservationModel->getReservationsByBusiness($businessId, $date);
        return $this->response->setJSON($reservations);
    }

    private function processCreate()
    {
        $rules = [
            'business_id' => 'required|integer',
            'reservation_date' => 'required|valid_date[Y-m-d]',
            'reservation_time' => 'required|regex_match[/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/]',
            'notes' => 'permit_empty',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'user_id' => session()->get('user_id'),
            'business_id' => $this->request->getPost('business_id'),
            'reservation_date' => $this->request->getPost('reservation_date'),
            'reservation_time' => $this->request->getPost('reservation_time'),
            'status' => 'pending',
            'notes' => $this->request->getPost('notes'),
        ];

        if (!$this->reservationModel->checkAvailability($data['business_id'], $data['reservation_date'], $data['reservation_time'])) {
            return redirect()->back()->withInput()->with('error', 'Time slot is not available');
        }

        if ($this->reservationModel->insert($data)) {
            return redirect()->to('/reservations')->with('success', 'Reservation created successfully');
        }

        return redirect()->back()->withInput()->with('error', 'Failed to create reservation');
    }

    private function processUpdate($id)
    {
        $rules = [
            'business_id' => 'required|integer',
            'reservation_date' => 'required|valid_date[Y-m-d]',
            'reservation_time' => 'required|regex_match[/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/]',
            'notes' => 'permit_empty',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'business_id' => $this->request->getPost('business_id'),
            'reservation_date' => $this->request->getPost('reservation_date'),
            'reservation_time' => $this->request->getPost('reservation_time'),
            'notes' => $this->request->getPost('notes'),
        ];

        if (!$this->reservationModel->checkAvailability($data['business_id'], $data['reservation_date'], $data['reservation_time'])) {
            return redirect()->back()->withInput()->with('error', 'Time slot is not available');
        }

        if ($this->reservationModel->update($id, $data)) {
            return redirect()->to('/reservations')->with('success', 'Reservation updated successfully');
        }

        return redirect()->back()->withInput()->with('error', 'Failed to update reservation');
    }
}