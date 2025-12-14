<?php
class Database {
    private static $host = 'localhost';
    private static $db   = 'pms'; 
    private static $user = 'root'; 
    private static $pass = 'Adey@@1997'; 
    private static $charset = 'utf8mb4';

    public static function connect() {
        $dsn = "mysql:host=" . self::$host . ";dbname=" . self::$db . ";charset=" . self::$charset;
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];
        try {
            return  new PDO($dsn, self::$user, self::$pass, $options);
        } catch (\PDOException $e) {
            // Log error and provide a generic message
            error_log("Database connection error: " . $e->getMessage());
            die("Database connection failed.");
        }
    }
}
