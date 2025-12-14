<?php

require_once 'BaseModel.php';

class ExitRequest extends BaseModel {
    
    const STATUS_PENDING = 'pending';

    public function create(int $studentId): int {
        $sql = "INSERT INTO exit_requests (student_id, status, request_date) 
                VALUES (?, ?, NOW())";
        
        $stmt = $this->db->prepare($sql);
        // Start status is always 'pending' (Dormitory Role requirement)
        $stmt->execute([$studentId, self::STATUS_PENDING]);
        
        return (int)$this->db->lastInsertId();
    }
    
    public function getPendingRequests(string $searchQuery = ''): array {
        // Assume a 'students' table with id, student_id, full_name
        $sql = "SELECT er.id, er.request_date, er.status, 
                        s.student_id, s.full_name
                FROM exit_requests er
                JOIN students s ON er.student_id = s.id
                WHERE er.status = :status_pending";
        
        $params = [':status_pending' => self::STATUS_PENDING];

        // 1. Add search conditions if a query is provided
        if (!empty($searchQuery)) {
            $sql .= " AND (
                CAST(er.id AS CHAR) = :search_exact_id OR
                s.student_id LIKE :search_id_partial OR
                s.full_name LIKE :search_name_partial
            )";

            $params[':search_exact_id'] = $searchQuery;
            
            $partialSearch = "%{$searchQuery}%";
            $params[':search_id_partial'] = $partialSearch;
            $params[':search_name_partial'] = $partialSearch;
        }

        $sql .= " ORDER BY er.request_date DESC";
                
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params); // Execute with the compiled parameters
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    

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
    

   