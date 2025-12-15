<?php

require_once dirname(__DIR__) . '/models/Student.php';
require_once dirname(__DIR__) . '/models/PropertyCategory.php';
require_once dirname(__DIR__) . '/models/StudentProperty.php';
require_once dirname(__DIR__) . '/models/PropertyDetail.php';

class StudentController {
    private $studentModel;
    private $categoryModel;
    private $propertyModel;
    private $detailModel;

    public function __construct() {
        $this->studentModel = new Student();
        $this->categoryModel = new PropertyCategory();
        $this->propertyModel = new StudentProperty();
        $this->detailModel = new PropertyDetail();
    }

    public function index() {
        $students = $this->studentModel->all(10); 
        require_once dirname(__DIR__) . '/views/students/index.php';
    }

    public function store() {
        header('Content-Type: application/json');
        try {
            // 1. Validate Basic Data
            $input = $_POST;
            if (empty($input['full_name']) || empty($input['student_id'])) {
                throw new Exception("Full Name and Student ID are required.", 400);
            }

            $studentIdPk = $this->studentModel->create($input, $_FILES);
            
            $properties = json_decode($input['properties_data'] ?? '[]', true);
            foreach ($properties as $property) {
                $categoryId = (int)($property['category_id'] ?? 0);
                $quantity = (int)($property['quantity'] ?? 0);
                $requiresDetail = (int)($property['requires_detail'] ?? 0);

                if ($categoryId > 0 && $quantity > 0) {
                    $studentPropertyId = $this->propertyModel->create($studentIdPk, $categoryId, $quantity);

                    if ($requiresDetail === 1) {
                        $model = trim($property['model'] ?? '');
                        $serial = trim($property['serial_number'] ?? '');

                        if (empty($model) || empty($serial)) {
                            error_log("Missing laptop details for student_property_id: {$studentPropertyId}");
                        } else {
                            $this->detailModel->create($studentPropertyId, $model, $serial);
                        }
                    }
                }
            }

            echo json_encode(['success' => true, 'message' => "Student {$input['full_name']} registered successfully!"]);

        } catch (Exception $e) {
            http_response_code($e->getCode() >= 400 ? $e->getCode() : 500);
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }

public static function edit()
{
    header('Content-Type: application/json');

    if (empty($_GET['id'])) {
        echo json_encode(['success' => false, 'message' => 'Missing student id']);
        exit;
    }

    $id = (int) $_GET['id'];

    try {
        $db = Database::connect();

        // Fetch student
        $stmt = $db->prepare("
            SELECT full_name, student_id, batch, department, block, room, profile_photo, created_at
            FROM students
            WHERE id = :id
            LIMIT 1
        ");
        $stmt->execute([':id' => $id]);
        $student = $stmt->fetch();

        if (!$student) {
            echo json_encode(['success' => false, 'message' => 'Student not found']);
            exit;
        }

        // Fetch properties + details
        $stmt2 = $db->prepare("
            SELECT 
                sp.id AS student_property_id,
                sp.category_id,
                sp.quantity,
                c.name AS category_name,
                c.requires_detail,
                pd.id AS detail_id,
                pd.model,
                pd.serial_number
            FROM student_properties sp
            JOIN property_categories c ON c.id = sp.category_id
            LEFT JOIN property_details pd ON pd.student_property_id = sp.id
            WHERE sp.student_id = :student_id
            ORDER BY sp.id
        ");
        $stmt2->execute([':student_id' => $id]);
        $properties = $stmt2->fetchAll();

        echo json_encode([
            'success' => true,
            'data' => [
                'student' => $student,
                'properties' => $properties
            ]
        ]);
        exit;

    } catch (Throwable $e) {
        error_log($e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Server error']);
        exit;
    }
}


public static function update()
{
    header('Content-Type: application/json');

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode(['success' => false, 'message' => 'Invalid request']);
        exit;
    }

    $id = (int) ($_POST['id'] ?? 0);
    if ($id <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid student id']);
        exit;
    }

    $properties = json_decode($_POST['properties_data'] ?? '[]', true);

    try {
        $db = Database::connect();
        $db->beginTransaction();

        /* ---------- Handle Profile Photo Upload ---------- */
        $photoPath = null;
        if (!empty($_FILES['profile_photo']) && $_FILES['profile_photo']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = __DIR__ . '/../uploads/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

            $fileTmp = $_FILES['profile_photo']['tmp_name'];
            $fileName = time() . '_' . preg_replace('/[^a-zA-Z0-9_\.-]/', '_', $_FILES['profile_photo']['name']);
            $dest = $uploadDir . $fileName;

            if (move_uploaded_file($fileTmp, $dest)) {
                // store relative path for DB
                $photoPath = '/uploads/' . $fileName;
            } else {
                throw new Exception("Failed to upload profile photo");
            }
        }

        /* ---------- Update student ---------- */
        $sql = "
            UPDATE students SET
                full_name = :full_name,
                student_id = :student_id,
                batch = :batch,
                department = :department,
                block = :block,
                room = :room
        ";

        $params = [
            ':full_name' => trim($_POST['full_name']),
            ':student_id' => trim($_POST['student_id']),
            ':batch' => trim($_POST['batch']),
            ':department' => trim($_POST['department']),
            ':block' => trim($_POST['block']),
            ':room' => trim($_POST['room']),
            ':id' => $id
        ];

        if ($photoPath) {
            $sql .= ", profile_photo = :profile_photo";
            $params[':profile_photo'] = $photoPath;
        }

        $sql .= " WHERE id = :id";
        $stmt = $db->prepare($sql);
        $stmt->execute($params);

        /* ---------- Remove old properties ---------- */
        $db->prepare("DELETE FROM student_properties WHERE student_id = :id")
           ->execute([':id' => $id]);

        /* ---------- Insert new properties & details ---------- */
        $insertProp = $db->prepare("
            INSERT INTO student_properties (student_id, category_id, quantity)
            VALUES (:student_id, :category_id, :quantity)
        ");

        $insertDetail = $db->prepare("
            INSERT INTO property_details (student_property_id, model, serial_number)
            VALUES (:sp_id, :model, :serial)
        ");

        foreach ($properties as $p) {
            $insertProp->execute([
                ':student_id' => $id,
                ':category_id' => (int)$p['category_id'],
                ':quantity' => (int)$p['quantity']
            ]);

            $studentPropertyId = $db->lastInsertId();

            // Insert details only if provided
            if (!empty($p['model']) || !empty($p['serial_number'])) {
                $insertDetail->execute([
                    ':sp_id' => $studentPropertyId,
                    ':model' => $p['model'] ?? null,
                    ':serial' => $p['serial_number'] ?? null
                ]);
            }
        }

        $db->commit();

        echo json_encode(['success' => true, 'message' => 'Student updated successfully']);
        exit;

    } catch (Throwable $e) {
        if ($db->inTransaction()) $db->rollBack();
        error_log($e->getMessage());

        echo json_encode(['success' => false, 'message' => 'Server error']);
        exit;
    }
}


    // POST /students/delete
    public static function destroy()
    {

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            exit;
        }

        $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
        if ($id <= 0) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Missing student id']);
            exit;
        }

        try {
          $db = Database::connect();
            $db->beginTransaction();

            // optional: delete uploaded profile file from disk (fetch path first)
            $stmtFetch = $db->prepare("SELECT profile_photo FROM students WHERE id = :id");
            $stmtFetch->execute([':id' => $id]);
            $row = $stmtFetch->fetch(PDO::FETCH_ASSOC);
            if ($row && !empty($row['profile_photo'])) {
                $filePath = __DIR__ . '/../public' . $row['profile_photo'];
                if (file_exists($filePath)) {
                    @unlink($filePath);
                }
            }

            // delete property assignments
            $stmtProps = $db->prepare("DELETE FROM student_properties WHERE student_id = :student_id");
            $stmtProps->execute([':student_id' => $id]);

            // delete student
            $stmt = $db->prepare("DELETE FROM students WHERE id = :id");
            $stmt->execute([':id' => $id]);

            $db->commit();

            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'message' => 'Student deleted successfully']);
            exit;
        } catch (Exception $e) {
            if ($db && $db->inTransaction()) $db->rollBack();
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
            exit;
        }
    }

    public function loadCategories() {
        header('Content-Type: application/json');
        try {
            $categories = $this->categoryModel->all();
            echo json_encode(['success' => true, 'data' => $categories]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Failed to load categories.']);
        }
    }

    public function addCategory() {
        header('Content-Type: application/json');
        try {
            $name = trim($_POST['category_name'] ?? '');
            $requiresDetail = isset($_POST['requires_detail']) && $_POST['requires_detail'] === '1' ? 1 : 0;

            if (empty($name)) {
                 throw new Exception("Category name cannot be empty.", 400);
            }

            $newId = $this->categoryModel->create($name, $requiresDetail);
            
            echo json_encode([
                'success' => true,
                'message' => "Category '{$name}' added successfully.",
                'new_category' => ['id' => $newId, 'name' => $name, 'requires_detail' => $requiresDetail]
            ]);

        } catch (Exception $e) {
            http_response_code($e->getCode() >= 400 ? $e->getCode() : 500);
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function show($id = null) {
        header('Content-Type: application/json');
        
        $student = null;
        try {
            if ($id) {
                $student = $this->studentModel->findById($id);
            } elseif (isset($_GET['student_id'])) {
                $student = $this->studentModel->findByStudentId($_GET['student_id']);
            }

            if (!$student) {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'Student not found.']);
                return;
            }

            $properties = $this->propertyModel->getByStudentId($student['id']);
            
            $processedProperties = [];
            foreach ($properties as $prop) {
                $prop['details'] = null;
                if ($prop['requires_detail']) {
                    $prop['details'] = $this->detailModel->getByStudentPropertyId($prop['id']);
                }
                $processedProperties[] = $prop;
            }
            
            $student['properties'] = $processedProperties;

            echo json_encode(['success' => true, 'data' => $student]);

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Error retrieving student details.']);
        }
    }
}

