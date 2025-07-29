<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\CartModel;

class AuthController extends BaseController
{
    protected $userModel;
    protected $cartModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->cartModel = new CartModel();
    }

    public function login()
    {
        if (session()->get('user_id')) {
            return redirect()->to('/');
        }

        $data = [
            'title' => 'Login - Nandini Hub'
        ];

        return view('auth/login', $data);
    }

    public function register()
    {
        if (session()->get('user_id')) {
            return redirect()->to('/');
        }

        $data = [
            'title' => 'Register - Nandini Hub'
        ];

        return view('auth/register', $data);
    }

    public function processLogin()
    {
        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');

        $user = $this->userModel->getUserByEmail($email);

        if ($user && $this->userModel->verifyPassword($password, $user['password'])) {
            // Set session data
            session()->set([
                'user_id' => $user['id'],
                'user_email' => $user['email'],
                'user_name' => $this->userModel->getFullName($user),
                'role' => $user['role'],
                'is_logged_in' => true
            ]);

            // Transfer cart items from session to user
            $sessionId = session()->session_id;
            $this->cartModel->transferCartToUser($sessionId, $user['id']);

            session()->setFlashdata('success', 'Welcome back, ' . $user['first_name'] . '!');

            // Redirect to intended page or home
            $redirectTo = session()->get('redirect_to') ?? '/';
            session()->remove('redirect_to');

            return redirect()->to($redirectTo);
        } else {
            session()->setFlashdata('error', 'Invalid email or password');
            return redirect()->back()->withInput();
        }
    }

    public function processRegister()
    {
        $rules = [
            'first_name' => 'required|min_length[2]|max_length[100]',
            'last_name'  => 'required|min_length[2]|max_length[100]',
            'email'      => 'required|valid_email|is_unique[users.email]',
            'password'   => 'required|min_length[6]',
            'confirm_password' => 'required|matches[password]',
            'phone'      => 'permit_empty|min_length[10]|max_length[20]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $userData = [
            'first_name' => $this->request->getPost('first_name'),
            'last_name'  => $this->request->getPost('last_name'),
            'email'      => $this->request->getPost('email'),
            'password'   => $this->request->getPost('password'),
            'phone'      => $this->request->getPost('phone'),
            'is_active'  => 1
        ];

        if ($this->userModel->insert($userData)) {
            $userId = $this->userModel->getInsertID();

            // Set session data
            session()->set([
                'user_id' => $userId,
                'user_email' => $userData['email'],
                'user_name' => $userData['first_name'] . ' ' . $userData['last_name'],
                'is_logged_in' => true
            ]);

            // Transfer cart items from session to user
            $sessionId = session()->session_id;
            $this->cartModel->transferCartToUser($sessionId, $userId);

            session()->setFlashdata('success', 'Registration successful! Welcome to Nandini Hub.');
            return redirect()->to('/');
        } else {
            session()->setFlashdata('error', 'Registration failed. Please try again.');
            return redirect()->back()->withInput();
        }
    }

    public function logout()
    {
        session()->destroy();
        session()->setFlashdata('success', 'You have been logged out successfully.');
        return redirect()->to('/');
    }

    public function profile()
    {
        if (!session()->get('user_id')) {
            session()->set('redirect_to', current_url());
            return redirect()->to('/login');
        }

        $user = $this->userModel->find(session()->get('user_id'));

        $data = [
            'title' => 'My Profile - Nandini Hub',
            'user' => $user
        ];

        return view('auth/profile', $data);
    }

    public function updateProfile()
    {
        if (!session()->get('user_id')) {
            return redirect()->to('/login');
        }

        $userId = session()->get('user_id');

        // Debug: Log the incoming data
        log_message('info', 'Profile update attempt for user ID: ' . $userId);
        log_message('info', 'POST data: ' . json_encode($this->request->getPost()));

        $rules = [
            'first_name' => 'required|min_length[2]|max_length[100]|alpha_space',
            'last_name'  => 'required|min_length[2]|max_length[100]|alpha_space',
            'email'      => "required|valid_email|is_unique[users.email,id,{$userId}]",
            'phone'      => 'permit_empty|min_length[10]|max_length[15]|numeric',
            'address'    => 'permit_empty|max_length[500]',
            'city'       => 'permit_empty|max_length[100]|alpha_space',
            'state'      => 'permit_empty|max_length[100]|alpha_space',
            'pincode'    => 'permit_empty|exact_length[6]|numeric',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $userData = [
            'first_name' => trim($this->request->getPost('first_name')),
            'last_name'  => trim($this->request->getPost('last_name')),
            'email'      => trim($this->request->getPost('email')),
            'phone'      => trim($this->request->getPost('phone')),
            'address'    => trim($this->request->getPost('address')),
            'city'       => trim($this->request->getPost('city')),
            'state'      => trim($this->request->getPost('state')),
            'pincode'    => trim($this->request->getPost('pincode')),
        ];

        // Remove empty values to avoid overwriting with empty strings
        $userData = array_filter($userData, function ($value) {
            return $value !== '';
        });

        // Temporarily disable model validation since we're validating in controller
        $this->userModel->skipValidation(true);

        try {
            if ($this->userModel->update($userId, $userData)) {
                // Update session data
                session()->set([
                    'user_email' => $userData['email'],
                    'user_name' => $userData['first_name'] . ' ' . $userData['last_name']
                ]);

                session()->setFlashdata('success', 'Profile updated successfully.');
                log_message('info', 'Profile updated successfully for user ID: ' . $userId);
            } else {
                // Log the error for debugging
                $errors = $this->userModel->errors();
                log_message('error', 'Profile update failed: ' . json_encode($errors));
                session()->setFlashdata('error', 'Failed to update profile. Please check your information.');
            }
        } catch (\Exception $e) {
            // Log the exception
            log_message('error', 'Profile update exception: ' . $e->getMessage());
            session()->setFlashdata('error', 'An error occurred while updating your profile: ' . $e->getMessage());
        }

        return redirect()->to('/profile');
    }

    public function changePassword()
    {
        if (!session()->get('user_id')) {
            session()->set('redirect_to', current_url());
            return redirect()->to('/login');
        }

        $data = [
            'title' => 'Change Password - Nandini Hub'
        ];

        return view('auth/change_password', $data);
    }

    public function updatePassword()
    {
        if (!session()->get('user_id')) {
            return redirect()->to('/login');
        }

        $userId = session()->get('user_id');

        $rules = [
            'current_password' => 'required',
            'new_password' => 'required|min_length[6]|max_length[255]',
            'confirm_password' => 'required|matches[new_password]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Get current user data
        $user = $this->userModel->find($userId);
        if (!$user) {
            session()->setFlashdata('error', 'User not found.');
            return redirect()->to('/profile');
        }

        // Verify current password
        if (!$this->userModel->verifyPassword($this->request->getPost('current_password'), $user['password'])) {
            session()->setFlashdata('error', 'Current password is incorrect.');
            return redirect()->back();
        }

        // Update password
        $userData = [
            'password' => $this->request->getPost('new_password')
        ];

        // The model will automatically hash the password
        if ($this->userModel->update($userId, $userData)) {
            session()->setFlashdata('success', 'Password updated successfully.');
            log_message('info', 'Password updated successfully for user ID: ' . $userId);
        } else {
            session()->setFlashdata('error', 'Failed to update password.');
            log_message('error', 'Password update failed for user ID: ' . $userId);
        }

        return redirect()->to('/profile');
    }
}
