<?php
session_start();
require_once  './config/db.php';

// Controllers
require_once  'controllers/AuthController.php';

$auth = new AuthController();

// Simple route map
$paths = [
    '/login' => function() use ($auth) {
        require  'views/login.php';
    },
    '/logout' => function() use ($auth) {
        $auth->logout();
    },
    '/dashboard' => function() {
        require  'views/dashboard.php';
    },
];

$url = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

if (isset($paths[$url])) {
    $paths[$url](); // Call the closure
} else {
    // Custom 404
    echo "<h1>404 - Page Not Found</h1>";
}