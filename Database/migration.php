
<?php

include DIR . '/../config/db.php';

// Load SQL from database/schema.sql
$sql = file_get_contents(DIR . '/schema.sql');

// if (!$sql) {
//     die("Could not read schema.sql");
// }

// Generate hashed passwords
$gatePass = password_hash('gate_admin', PASSWORD_BCRYPT);
$dormPass = password_hash('dorm_admin', PASSWORD_BCRYPT);

// Replace placeholders
$sql = preg_replace('/\{bcrypt_password_here\}/', $gatePass, $sql, 1);
$sql = preg_replace('/\{bcrypt_password_here\}/', $dormPass, $sql, 1);

try {
    $pdo->exec($sql);
    echo "Database schema created and seeded successfully.";
} catch (PDOException $e) {
    echo "Error creating schema: " . $e->getMessage();
}
