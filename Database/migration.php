<?php
// database/migration.php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/db.php';

// Load SQL from schema (as a string here to keep everything in one file)
$sql = <<<SQL
-- 1) users table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('gate','dormitory') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- 2) students table
CREATE TABLE IF NOT EXISTS students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(150) NOT NULL,
    student_id VARCHAR(50) UNIQUE NOT NULL,
    batch VARCHAR(50),
    department VARCHAR(100),
    block VARCHAR(50),
    room VARCHAR(50),
    profile_photo VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- 3) property_categories
CREATE TABLE IF NOT EXISTS property_categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) UNIQUE NOT NULL,
    requires_detail TINYINT(1) DEFAULT 0
);

-- 4) student_properties
CREATE TABLE IF NOT EXISTS student_properties (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    category_id INT NOT NULL,
    quantity INT NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_sp_student FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    CONSTRAINT fk_sp_category FOREIGN KEY (category_id) REFERENCES property_categories(id) ON DELETE CASCADE
);

-- 5) property_details
CREATE TABLE IF NOT EXISTS property_details (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_property_id INT NOT NULL,
    model VARCHAR(150),
    serial_number VARCHAR(150),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_pd_student_property FOREIGN KEY (student_property_id) REFERENCES student_properties(id) ON DELETE CASCADE
);

-- 6) exit_requests
CREATE TABLE IF NOT EXISTS exit_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    request_date DATE DEFAULT NULL,
    status ENUM('pending','checked','rejected') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_er_student FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE
);

-- 7) exit_request_items
CREATE TABLE IF NOT EXISTS exit_request_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    exit_request_id INT NOT NULL,
    category_id INT NOT NULL,
    quantity INT NOT NULL,
    model VARCHAR(150),
    serial_number VARCHAR(150),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_eri_request FOREIGN KEY (exit_request_id) REFERENCES exit_requests(id) ON DELETE CASCADE,
    CONSTRAINT fk_eri_category FOREIGN KEY (category_id) REFERENCES property_categories(id) ON DELETE CASCADE
);

-- 8) exit_verifications
CREATE TABLE IF NOT EXISTS exit_verifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    exit_request_id INT NOT NULL,
    gate_staff_id INT NOT NULL,
    verified_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    remarks TEXT,
    CONSTRAINT fk_ev_request FOREIGN KEY (exit_request_id) REFERENCES exit_requests(id) ON DELETE CASCADE,
    CONSTRAINT fk_ev_gate FOREIGN KEY (gate_staff_id) REFERENCES users(id)
);
SQL;

// Execute schema creation
try {
    $pdo->exec($sql);
    echo "✅ Database tables created successfully.\n";
} catch (PDOException $e) {
    die("❌ Error creating tables: " . $e->getMessage());
}

// Seed default users
try {
    $gatePass = password_hash('user00', PASSWORD_BCRYPT);
    $dormPass = password_hash('user00', PASSWORD_BCRYPT);

    $stmt = $pdo->prepare("INSERT IGNORE INTO users (username,password,role) VALUES (?, ?, ?)");
    $stmt->execute(['user00', $gatePass, 'gate']);
    $stmt->execute(['user01', $dormPass, 'dormitory']);
    echo "✅ Default users seeded.\n";
} catch (PDOException $e) {
    die("❌ Error seeding users: " . $e->getMessage());
}

// Seed property categories
try {
    $categories = [
        ['Laptop', 1],
        ['Shirt', 0],
        ['Shoes', 0],
        ['Blanket', 0],
        ['Jacket', 0],
        ['Other', 1]
    ];

    $stmt = $pdo->prepare("INSERT IGNORE INTO property_categories (name, requires_detail) VALUES (?, ?)");
    foreach ($categories as $cat) {
        $stmt->execute($cat);
    }
    echo "✅ Property categories seeded.\n";
} catch (PDOException $e) {
    die("❌ Error seeding categories: " . $e->getMessage());
}

echo "🎉 Migration and seeding completed successfully.\n";
