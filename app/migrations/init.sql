-- Migration: Initial schema for ID System

CREATE TABLE IF NOT EXISTS users (
    id CHAR(36) PRIMARY KEY,
    role ENUM('SUPERADMIN', 'ADMIN', 'STUDENT', 'FACULTY') NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS profiles (
    user_id CHAR(36) PRIMARY KEY,
    full_name VARCHAR(255) NOT NULL,
    student_faculty_id VARCHAR(100) NOT NULL,
    department VARCHAR(255) NOT NULL,
    year VARCHAR(50) NOT NULL,
    course_grade_strand VARCHAR(255) NOT NULL,
    updated_at DATETIME NOT NULL,
    CONSTRAINT fk_profiles_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS appointments (
    id CHAR(36) PRIMARY KEY,
    user_id CHAR(36) NOT NULL,
    reason TEXT NOT NULL,
    id_picture_url VARCHAR(500) NULL,
    signature_image VARCHAR(500) NULL,
    contact_person_name VARCHAR(255) NULL,
    contact_person_address VARCHAR(500) NULL,
    contact_person_number VARCHAR(50) NULL,
    scheduled_at DATETIME NOT NULL,
    status ENUM('PENDING', 'APPROVED', 'RESCHEDULED', 'CANCELED') NOT NULL DEFAULT 'PENDING',
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    CONSTRAINT fk_appointments_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_appointments_user (user_id),
    INDEX idx_appointments_status (status),
    INDEX idx_appointments_scheduled_at (scheduled_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS appointment_status_history (
    id INT AUTO_INCREMENT PRIMARY KEY,
    appointment_id CHAR(36) NOT NULL,
    old_status ENUM('PENDING', 'APPROVED', 'RESCHEDULED', 'CANCELED') NOT NULL,
    new_status ENUM('PENDING', 'APPROVED', 'RESCHEDULED', 'CANCELED') NOT NULL,
    changed_by CHAR(36) NOT NULL,
    changed_at DATETIME NOT NULL,
    CONSTRAINT fk_history_appointment FOREIGN KEY (appointment_id) REFERENCES appointments(id) ON DELETE CASCADE,
    CONSTRAINT fk_history_changed_by FOREIGN KEY (changed_by) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_history_appointment (appointment_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS qr_codes (
    id CHAR(36) PRIMARY KEY,
    appointment_id CHAR(36) NOT NULL UNIQUE,
    qr_data TEXT NOT NULL,
    generated_at DATETIME NOT NULL,
    CONSTRAINT fk_qr_codes_appointment FOREIGN KEY (appointment_id) REFERENCES appointments(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS admin_login_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    admin_user_id CHAR(36) NOT NULL,
    logged_in_at DATETIME NOT NULL,
    CONSTRAINT fk_admin_logs_user FOREIGN KEY (admin_user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_admin_logs_user (admin_user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO users (id, role, email, password_hash, created_at, updated_at)
SELECT
    '11111111-1111-1111-1111-111111111111',
    'SUPERADMIN',
    'superadmin@example.com',
    '$2y$10$iCmcqdIhoz813aEkKHAFhuarkEpK5r1RB60nTRUG7OEeci4jVHkFu',
    NOW(),
    NOW()
WHERE NOT EXISTS (SELECT 1 FROM users WHERE email = 'superadmin@example.com');

INSERT INTO profiles (user_id, full_name, student_faculty_id, department, year, course_grade_strand, updated_at)
SELECT
    '11111111-1111-1111-1111-111111111111',
    'System Super Admin',
    'ADMIN-0001',
    'Administration',
    'N/A',
    'N/A',
    NOW()
WHERE NOT EXISTS (SELECT 1 FROM profiles WHERE user_id = '11111111-1111-1111-1111-111111111111');
