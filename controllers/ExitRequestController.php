<?php

require_once dirname(__DIR__) . '/models/Student.php';          
require_once dirname(__DIR__) . '/models/StudentProperty.php'; 
require_once dirname(__DIR__) . '/models/ExitRequest.php';
require_once dirname(__DIR__) . '/models/ExitRequestItem.php';
require_once dirname(__DIR__) . '/models/ExitVerification.php'; 

class ExitRequestController {
    private $requestModel;
    private $itemModel;
    private $studentModel;
    private $propertyModel;
    private $verificationModel;

    public function __construct() {
        $this->requestModel = new ExitRequest();
        $this->itemModel = new ExitRequestItem();
        $this->studentModel = new Student();
        $this->propertyModel = new StudentProperty(); 
        $this->verificationModel = new ExitVerification();
    }



    public function searchStudent() {
        $studentId = trim($_GET['student_id'] ?? '');

        if (empty($studentId)) {
            $this->jsonResponse(false, null, "Student ID is required.", 400);
        }

        try {
            $student = $this->studentModel->findByStudentId($studentId);
            if (!$student) {
                $this->jsonResponse(false, null, "Student not found.", 404);
            }

            $properties = $this->propertyModel->getPropertiesForExitRequest($student['id']);
            
            if (empty($properties)) {
                $this->jsonResponse(false, null, "Student found, but has no registered properties.", 400);
            }

            $data = [
                'student' => $student,
                'owned_properties' => $properties
            ];

            $this->jsonResponse(true, $data, "Student details retrieved.");

        } catch (Exception $e) {
            $this->jsonResponse(false, null, "Server error: " . $e->getMessage(), 500);
        }
    }


    public function pending() {
        try {
            $requests = $this->requestModel->getPendingRequests();
            require_once dirname(__DIR__)  . '/views/gate_pending_list.php';
        } catch (Exception $e) {
            $requests = [];
            $errorMessage = "Error retrieving pending requests: " . $e->getMessage();
            require_once dirname(__DIR__)  . '/views/gate_pending_list.php';
        }
    }

    public function show() {
        $requestId = (int)($_GET['id'] ?? 0);
        
        try {
            $request = $this->requestModel->findById($requestId);
            if (!$request) {
                http_response_code(404);
                die("Request not found.");
            }

            $items = $this->itemModel->getItemsByRequestId($requestId); 
            
            require_once dirname(__DIR__)  . '/views/gate_detail_view.php';
        } catch (Exception $e) {
            http_response_code(500);
            die("Server error while loading details: " . $e->getMessage());
        }
    }
    private function jsonResponse(bool $success, $data = null, string $message = null, int $httpCode = 200) {
        http_response_code($httpCode);
        header('Content-Type: application/json');
        echo json_encode([
            'success' => $success,
            'data' => $data,
            'message' => $message
        ]);
        exit;
    }
}