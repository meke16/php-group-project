<?php
// /models/Student.php
require_once 'BaseModel.php';

class Student extends BaseModel {
    // Directory to store uploaded profile photos (must be writable)
    private $uploadDir = 'public/uploads/';

    public function __construct() {
        parent::__construct();
        if (!is_dir($this->uploadDir)) {
            mkdir($this->uploadDir, 0777, true);
        }
    }

    public function create($data, $file) {
        $this->db->beginTransaction();
        try {
            // 1. Handle Photo Upload
            $photoPath = $this->handlePhotoUpload($file);

            // 2. Insert Student
            $sql = "INSERT INTO students (full_name, student_id, batch, department, block, room, profile_photo) 
                    VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                $data['full_name'], $data['student_id'], $data['batch'], $data['department'], 
                $data['block'], $data['room'], $photoPath
            ]);
            $studentIdPk = $this->db->lastInsertId();

            $this->db->commit();
            return $studentIdPk;

        } catch (PDOException $e) {
            if ($this->db->inTransaction()) { $this->db->rollBack(); }
            // Clean up photo on failure
            if ($photoPath && file_exists($photoPath)) { unlink($photoPath); }
            
            if ($e->getCode() === '23000') {
                throw new Exception("Error: Student ID already exists.", 409); // Conflict
            }
            throw new Exception("Database Error: " . $e->getMessage(), 500);
        } catch (Exception $e) {
             if ($this->db->inTransaction()) { $this->db->rollBack(); }
             throw $e;
        }
    }

    private function handlePhotoUpload($file) {
        if (!isset($file['profile_photo']) || $file['profile_photo']['error'] !== UPLOAD_ERR_OK) {
            return null;
        }

        $tmpPath = $file['profile_photo']['tmp_name'];
        $fileName = basename($file['profile_photo']['name']);
        $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $allowedExt = ['jpg', 'jpeg', 'png', 'gif'];
        
        if (!in_array($fileExt, $allowedExt)) {
            throw new Exception("Invalid file type. Only JPG, JPEG, PNG, GIF are allowed.", 400);
        }

        $newFileName = uniqid('profile_') . '.' . $fileExt;
        $destPath = $this->uploadDir . $newFileName;

        if (move_uploaded_file($tmpPath, $destPath)) {
            return $destPath;
        } else {
            throw new Exception("Error uploading profile photo.", 500);
        }
    }

    public function all($limit = 10) {
        $sql = "SELECT id, full_name, student_id, batch, department FROM students ORDER BY created_at DESC LIMIT ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    }

    public function findById($id) {
        $sql = "SELECT * FROM students WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function findByStudentId($studentId) {
        $sql = "SELECT * FROM students WHERE student_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$studentId]);
        return $stmt->fetch();
    }
}
