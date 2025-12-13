<?php
// controllers/AuthController.php
session_start(); // start session for login

require_once __DIR__. '/../config/db.php';
require_once __DIR__. '/../models/User.php';

class AuthController {
    
    // Handle login form submission
    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';

            if (!$username || !$password) {
                $_SESSION['error'] = "Username and password are required.";
                header("Location: login");
                exit;
            }

            $user = User::findByUsername($username);

            if ($user && password_verify($password, $user['password'])) {
                // Successful login
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];

                header("Location: dashboard");
                exit;
            } else {
                $_SESSION['error'] = "Invalid username or password.";
                header("Location: login");
                exit;
            }
        }
    }

    // Handle logout
    public function logout() {
        session_destroy();
        header("Location: login");
        exit;
    }

    // Check if user is logged in
    public static function checkAuth() {
        return isset($_SESSION['user_id']);
    }

    // Check if logged-in user has a specific role
    public static function checkRole($role) {
        return isset($_SESSION['role']) && $_SESSION['role'] === $role;
    }
}