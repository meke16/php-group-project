<?php
// /models/ExitRequestItem.php

require_once 'BaseModel.php';

class ExitRequestItem extends BaseModel {
    
    /**
     * Inserts an item into the request.
     * @return int The ID of the new exit_request_items record.
     */
    public function create(int $exitRequestId, int $categoryId, int $quantity): int {
        $sql = "INSERT INTO exit_request_items (exit_request_id, category_id, quantity) 
                VALUES (?, ?, ?)";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$exitRequestId, $categoryId, $quantity]);
        
        return (int)$this->db->lastInsertId();
    }
    
    /**
     * Retrieves items for a specific request, including category name.
     * Assumes property_categories has an 'id' and 'name' column.
     * @return array
     */
    public function getItemsByRequestId(int $requestId): array {
        $sql = "SELECT eri.quantity, pc.name AS category_name
                FROM exit_request_items eri
                JOIN property_categories pc ON eri.category_id = pc.id
                WHERE eri.exit_request_id = ?";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$requestId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}