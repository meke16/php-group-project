<?php
// /models/BaseModel.php (Used by other models)

abstract class BaseModel {
    protected $db;

    public function construct() {
        // Assume Database class from /config/db.php is accessible
        require_once dirname(__DIR__) . '/config/db.php';
        $this->db = Database::connect();
    }
}
