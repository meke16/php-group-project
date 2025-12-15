<?php
session_start();
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../models/User.php';

class AuthController {

    private $userModel;

    public function __construct() {
        $pdo = Database::connect(); 
        $this->userModel = new User($pdo);
    }

    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';

            if (!$username || !$password) {
                $_SESSION['error'] = "Username and password are required.";
                header("Location: login");
                exit;
            }

            $user = $this->userModel->login($username, $password);

            if ($user) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['success'] = "Logged in successfully!";
                header("Location: dashboard");
                exit;
            } else {
                $_SESSION['error'] = "Invalid username or password.";
                header("Location: login");
                exit;
            }
        }
    }

    public function logout() {
        session_destroy();
        header("Location: login");
        exit;
    }

    public static function checkAuth() {
        return isset($_SESSION['user_id']);
    }

    public static function checkRole($role) {
        return isset($_SESSION['role']) && $_SESSION['role'] === $role;
    }
}