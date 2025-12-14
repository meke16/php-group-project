<?php
// /models/StudentProperty.php
require_once 'BaseModel.php';

class StudentProperty extends BaseModel
{


    public function create(int $studentId, int $categoryId, int $quantity): int
    {

        // This query assumes your student_properties table structure:
        // (id, student_id, category_id, quantity)
        $sql = "INSERT INTO student_properties (student_id, category_id, quantity) 
                VALUES (:student_id, :category_id, :quantity)";

        $stmt = $this->db->prepare($sql);

        $stmt->bindParam(':student_id', $studentId, PDO::PARAM_INT);
        $stmt->bindParam(':category_id', $categoryId, PDO::PARAM_INT);
        $stmt->bindParam(':quantity', $quantity, PDO::PARAM_INT);

        $stmt->execute();

        return $this->db->lastInsertId();
    }

    // Used by ExitRequestController::searchStudent
    public function getPropertiesForExitRequest(int $studentIdPk): array
    {
        // Joins with property_categories to get the name and groups by category.
        $sql = "SELECT 
                    sp.category_id, 
                    pc.name AS category_name,
                    SUM(sp.quantity) AS total_quantity
                FROM student_properties sp
                JOIN property_categories pc ON sp.category_id = pc.id
                WHERE sp.student_id = ?
                GROUP BY sp.category_id, pc.name";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$studentIdPk]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Used by ExitRequestController::store for Server-Side Validation
    public function isQuantityAvailable(int $studentIdPk, int $categoryId, int $requestedQuantity): bool
    {
        $sql = "SELECT SUM(quantity) 
                FROM student_properties 
                WHERE student_id = ? AND category_id = ?";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$studentIdPk, $categoryId]);

        $totalOwned = (int)$stmt->fetchColumn();

        return $totalOwned >= $requestedQuantity;
    }

    // Used for descriptive error messages in the controller
    public function getCategoryName(int $categoryId): array|false
    {
        $sql = "SELECT name FROM property_categories WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$categoryId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getByStudentId(int $studentIdPk): array
    {

        $sql = "SELECT 
                    sp.id, 
                    sp.category_id, 
                    sp.quantity, 
                    pc.name AS category_name,
                    pc.requires_detail
                FROM student_properties sp
                JOIN property_categories pc ON sp.category_id = pc.id
                WHERE sp.student_id = :student_id";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':student_id', $studentIdPk, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
