schema.sql

-- ---------------------------
-- Student Property Management - Clean Schema
-- ---------------------------

-- 1) users table (Gate & Dormitory staff)
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('gate', 'dormitory') NOT NULL,
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
    requires_detail BOOLEAN DEFAULT FALSE
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

-- 5) property_details (for laptops)
CREATE TABLE IF NOT EXISTS property_details (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_property_id INT NOT NULL,
    model VARCHAR(150),
    serial_number VARCHAR(150),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_pd_student_property FOREIGN KEY (student_property_id) REFERENCES student_properties(id) ON DELETE CASCADE
);

-- 6) exit_requests
-- request_date left nullable; use created_at for exact timestamp if needed
CREATE TABLE IF NOT EXISTS exit_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    request_date DATE DEFAULT NULL,
    status ENUM('pending', 'checked', 'rejected') DEFAULT 'pending',
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

-- 9) seed default users (INSERT IGNORE to not fail if re-run)
INSERT IGNORE INTO users (username, password, role) VALUES
('gateAdmin', '{bcrypt_password_here}', 'gate'),
('dormitory', '{bcrypt_password_here}', 'dormitory');

-- 10. Seed property categories
INSERT IGNORE INTO property_categories (name, requires_detail) VALUES
('Laptop', TRUE),
('Shirt', FALSE),
('Shoes', FALSE),
('Blanket', FALSE),
('Jacket', FALSE),
('Other', TRUE),