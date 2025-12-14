<?php

abstract class BaseModel {
    protected $db;

    public function __construct() {
        require_once dirname(__DIR__) . '/config/db.php';
        $this->db = Database::connect();
    }
    public function getDb(): PDO {
        return $this->db;
    }
}
