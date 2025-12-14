<?php

require_once  'models/BaseModel.php';
require_once  'controllers/StudentController.php';
require_once  'controllers/AuthController.php';
require_once  'controllers/ExitRequestController.php';


define('BASE_PATH', dirname(DIR));


$requestUri = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
$requestMethod = $_SERVER['REQUEST_METHOD'];


$studentController = new StudentController();
$auth = new AuthController();
$exitController = new ExitRequestController();

switch ($requestUri) {
    case '':
        require 'views/login.php';
        break;
    case 'login':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $auth->login();
        } else {
            require 'views/login.php';
        }
        break;
    case 'dashboard':
        require 'views/dashboard.php';
        break;
    case 'logout':
        $auth->logout();
        break;

    // --- STUDENT MANAGEMENT ROUTES ---
    case 'students':
        $studentController->index();
        break;
    case 'students/store':
        if ($requestMethod === 'POST') {
            $studentController->store();
        } else {
            http_response_code(405);
            die("Method Not Allowed");
        }
        break;
    case 'students/edit':
        if ($requestMethod === 'GET') {
            $studentController::edit();
        } else {
            http_response_code(405);
            die("Method Not Allowed");
        }
        break;
    case 'students/update':
        if ($requestMethod === 'POST') {
            $studentController::update();
        } else {
            http_response_code(405);
            die("Method Not Allowed");
        }
        break;
    case 'students/delete':
        if ($requestMethod === 'POST') {
            $studentController::destroy();
        } else {
            http_response_code(405);
            die("Method Not Allowed");
        }
        break;
    case 'students/search':
        $studentController->show(null);
        break;
    case 'students/show':
        $studentController->show($_GET['id'] ?? null);
        break;

    // --- CATEGORY MANAGEMENT ROUTES ---
    case 'categories/load':
        $studentController->loadCategories();
        break;
    case 'categories/store':
        if ($requestMethod === 'POST') {
            $studentController->addCategory();
        } else {
            http_response_code(405);
            die("Method Not Allowed");
        }
        break;

    // --- EXIT MANAGEMENT ROUTES (NEW) ---
    case 'exit/create':
        require 'views/exit.php';
        break;
    case 'exit/search_student':
        // DORMITORY SEARCH AJAX
        if ($requestMethod === 'GET') {
            $exitController->searchStudent();
        } else {
            http_response_code(405);
            die("Method Not Allowed");
        }
        break;
    case 'exit/pending':
        // GATE STAFF LIST VIEW
        $exitController->pending();
        break;
    case 'exit/show':
        // GATE STAFF DETAIL VIEW
        if ($requestMethod === 'GET') {
            $exitController->show();
        } else {
            http_response_code(405);
            die("Method Not Allowed");
        }
        break;
    default:
        http_response_code(404);
        echo "404 Not Found";
        break;
}
