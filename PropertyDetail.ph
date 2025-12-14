<?php

require_once 'BaseModel.php';

class PropertyDetail extends BaseModel {
    public function create($studentPropertyId, $model, $serialNumber) {
        $sql = "INSERT INTO property_details (student_property_id, model, serial_number) 
                VALUES (?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$studentPropertyId, $model, $serialNumber]);
        return $this->db->lastInsertId();
    }

    public function getByStudentPropertyId($studentPropertyId) {
        $sql = "SELECT model, serial_number FROM property_details WHERE student_property_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$studentPropertyId]);
        return $stmt->fetch();
    }
}
