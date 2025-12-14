<?php
// /models/PropertyCategory.php
require_once 'BaseModel.php';

class PropertyCategory extends BaseModel {
    public function all() {
        $stmt = $this->db->query("SELECT id, name, requires_detail FROM property_categories ORDER BY name");
        return $stmt->fetchAll();
    }

    public function create($name, $requiresDetail) {
        try {
            $sql = "INSERT INTO property_categories (name, requires_detail) VALUES (?, ?)";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$name, $requiresDetail]);
            return $this->db->lastInsertId();
        } catch (PDOException $e) {
            if ($e->getCode() === '23000') {
                throw new Exception("Category name '{$name}' already exists.", 409);
            }
            throw new Exception("Database Error: " . $e->getMessage(), 500);
        }
    }
}
