<?php
// /models/ExitVerification.php

require_once 'BaseModel.php';

class ExitVerification extends BaseModel {
    
    /**
     * Records the gate staff action (Verification or Rejection).
     */
    public function create(int $exitRequestId, int $gateStaffId, string $status, string $remarks = null): int {
        // Note: The status is redundant but useful for auditing/reporting
        $sql = "INSERT INTO exit_verifications (exit_request_id, gate_staff_id, status, remarks, verified_at) 
                VALUES (?, ?, ?, ?, NOW())";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$exitRequestId, $gateStaffId, $status, $remarks]);
        
        return (int)$this->db->lastInsertId();
    }
    
    /**
     * Checks if a request has already been verified or rejected.
     * Business Rule #6 safeguard.
     */
    public function hasBeenVerified(int $exitRequestId): bool {
        $sql = "SELECT 1 FROM exit_verifications WHERE exit_request_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$exitRequestId]);
        return $stmt->fetchColumn() !== false;
    }
}