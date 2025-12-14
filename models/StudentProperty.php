<?php
// /models/StudentProperty.php
require_once 'BaseModel.php';

class StudentProperty extends BaseModel {
    public function create($studentIdPk, $categoryId, $quantity) {
        $sql = "INSERT INTO student_properties (student_id, category_id, quantity) VALUES (?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$studentIdPk, $categoryId, $quantity]);
        return $this->db->lastInsertId();
    }

    public function getByStudentId($studentIdPk) {
        $sql = "SELECT sp.id, sp.quantity, pc.name AS category_name, pc.requires_detail
                FROM student_properties sp
                JOIN property_categories pc ON sp.category_id = pc.id
                WHERE sp.student_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$studentIdPk]);
        return $stmt->fetchAll();
    }
}
