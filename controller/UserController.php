<?php
require_once 'models/UserModel.php';

class UserController {
    private $userModel;

    public function __construct() {
        $this->userModel = new UserModel();
    }

    // Login function for all users
    public function login() {
        session_start();
        $error = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';

            // Authenticate user
            $user = $this->userModel->authenticate($username, $password);

            if ($user) {
                // Store session and redirect based on role
                $_SESSION['user_id'] = $user['UserID'];
                $_SESSION['role'] = $user['Role'];

                if ($user['Role'] === 'employee') {
                    header('Location: employee/dashboard');
                } elseif ($user['Role'] === 'customer') {
                    header('Location: customer/dashboard');
                }
                exit;
            } else {
                $error = 'Invalid username or password.';
            }
        }

        require 'views/login.php'; // Render login view
    }

    // Logout function
    public function logout() {
        session_start();
        session_destroy();
        header('Location: /'); // Redirect to home/login page
        exit;
    }
}
