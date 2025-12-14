<?php
// /models/ExitRequest.php

require_once 'BaseModel.php';

class ExitRequest extends BaseModel {
    
    // Statuses
    const STATUS_PENDING = 'pending';
    const STATUS_CHECKED = 'checked';
    const STATUS_REJECTED = 'rejected';

    /**
     * Creates a new exit request entry.
     * @return int The ID of the new exit_requests record.
     */
    public function create(int $studentId): int {
        $sql = "INSERT INTO exit_requests (student_id, status, request_date) 
                VALUES (?, ?, NOW())";
        
        $stmt = $this->db->prepare($sql);
        // Start status is always 'pending' (Dormitory Role requirement)
        $stmt->execute([$studentId, self::STATUS_PENDING]);
        
        return (int)$this->db->lastInsertId();
    }
    
    /**
     * Retrieves all pending requests for the Gate Staff list.
     * Includes basic student info for the list view.
     * @return array
     */
    public function getPendingRequests(): array {
        // Assume a 'students' table with id, student_id, full_name
        $sql = "SELECT er.id, er.request_date, er.status, 
                s.student_id, s.full_name
                FROM exit_requests er
                JOIN students s ON er.student_id = s.id
                WHERE er.status = ?";
                
        $stmt = $this->db->prepare($sql);
        $stmt->execute([self::STATUS_PENDING]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Finds a request by ID, including student details.
     * @return array|false
     */
    public function findById(int $id): array|false {
        // Include hostel info for Gate Staff
        $sql = "SELECT er.*, s.student_id, s.full_name, s.department, s.block, s.room
                FROM exit_requests er
                JOIN students s ON er.student_id = s.id
                WHERE er.id = ?";
                
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Updates the status of an exit request.
     */
    public function updateStatus(int $requestId, string $status): bool {
        if (!in_array($status, [self::STATUS_CHECKED, self::STATUS_REJECTED])) {
            return false;
        }
        $sql = "UPDATE exit_requests SET status = ? WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$status, $requestId]);
    }
}