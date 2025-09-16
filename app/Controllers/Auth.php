<?php

namespace App\Controllers;

use App\Models\UserModel;
use CodeIgniter\Controller;
use CodeIgniter\HTTP\ResponseInterface;

class Auth extends Controller
{
    public function login()
    {
        if ($this->request->getMethod() === 'post') {
            return $this->processLogin();
        }
        return view('auth/login');
    }

    public function register()
    {
        if ($this->request->getMethod() === 'post') {
            return $this->processRegister();
        }
        return view('auth/register');
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/auth/login');
    }

    private function processLogin()
    {
        $rules = [
            'email' => 'required|valid_email',
            'password' => 'required',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $userModel = new UserModel();
        $user = $userModel->where('email', $this->request->getPost('email'))->first();

        if ($user && $userModel->verifyPassword($this->request->getPost('password'), $user['password'])) {
            session()->set([
                'user_id' => $user['id'],
                'user_name' => $user['name'],
                'user_email' => $user['email'],
                'user_role' => $user['role'],
                'is_logged_in' => true,
            ]);

            if ($user['role'] === 'admin') {
                return redirect()->to('/admin/dashboard');
            } else {
                return redirect()->to('/reservations');
            }
        }

        return redirect()->back()->withInput()->with('error', 'Invalid email or password');
    }

    private function processRegister()
    {
        $rules = [
            'name' => 'required|min_length[2]',
            'email' => 'required|valid_email|is_unique[users.email]',
            'password' => 'required|min_length[8]',
            'password_confirm' => 'required|matches[password]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $userModel = new UserModel();
        $data = [
            'name' => $this->request->getPost('name'),
            'email' => $this->request->getPost('email'),
            'password' => $this->request->getPost('password'),
            'role' => 'customer',
        ];

        if ($userModel->insert($data)) {
            return redirect()->to('/auth/login')->with('success', 'Registration successful. Please login.');
        }

        return redirect()->back()->withInput()->with('error', 'Registration failed. Please try again.');
    }
}