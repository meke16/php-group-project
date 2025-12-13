<?php
require_once __DIR__ . '/../config/db.php';

class User {
    private $pdo;

    public function __construct($pdo)
    {
       $this->pdo = $pdo;
    }


    // Login: validate username + password
    public function login($username, $password) {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }

        return false;
    }

    public static function findByUsername($username) {
        global $pdo;
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        return $stmt->fetch();
    }
}