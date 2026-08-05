-- =====================================================
-- GRIN DENTAL - MOBILE APP API DATABASE MIGRATION
-- Run this after database.sql
-- =====================================================

USE `grin_dental`;

-- =====================================================
-- API AUTHENTICATION
-- =====================================================

-- API Tokens table for mobile app authentication
CREATE TABLE IF NOT EXISTS `api_tokens` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NOT NULL,
    `token` VARCHAR(64) NOT NULL UNIQUE,
    `device_type` ENUM('android', 'ios', 'web') DEFAULT 'android',
    `device_token` VARCHAR(255),
    `device_name` VARCHAR(100),
    `ip_address` VARCHAR(45),
    `last_activity` TIMESTAMP NULL,
    `expires_at` TIMESTAMP NULL,
    `status` ENUM('active', 'revoked') DEFAULT 'active',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
    INDEX `idx_token` (`token`),
    INDEX `idx_user_status` (`user_id`, `status`)
);

-- =====================================================
-- CHAT SYSTEM
-- =====================================================

-- Chat conversations between dentist and patient
CREATE TABLE IF NOT EXISTS `chat_conversations` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `dentist_id` INT NOT NULL,
    `patient_id` INT NOT NULL,
    `last_message` TEXT,
    `last_message_at` TIMESTAMP NULL,
    `unread_dentist` INT DEFAULT 0,
    `unread_patient` INT DEFAULT 0,
    `status` ENUM('active', 'archived') DEFAULT 'active',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`dentist_id`) REFERENCES `dentists` (`id`) ON DELETE CASCADE,
    FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE CASCADE,
    UNIQUE KEY `unique_conversation` (`dentist_id`, `patient_id`)
);

-- Chat messages
CREATE TABLE IF NOT EXISTS `chat_messages` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `conversation_id` INT NOT NULL,
    `sender_type` ENUM('dentist', 'patient') NOT NULL,
    `sender_id` INT NOT NULL,
    `message` TEXT NOT NULL,
    `message_type` ENUM('text', 'image', 'file') DEFAULT 'text',
    `file_path` VARCHAR(255),
    `is_read` ENUM('yes', 'no') DEFAULT 'no',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`conversation_id`) REFERENCES `chat_conversations` (`id`) ON DELETE CASCADE,
    INDEX `idx_conversation_time` (`conversation_id`, `created_at`)
);

-- =====================================================
-- TREATMENT PLANS (Dentist App)
-- =====================================================

-- Treatment plans created by dentists
CREATE TABLE IF NOT EXISTS `treatment_plans` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `patient_id` INT NOT NULL,
    `dentist_id` INT NOT NULL,
    `title` VARCHAR(255) NOT NULL,
    `description` TEXT,
    `total_cost` DECIMAL(10,2) DEFAULT 0.00,
    `estimated_sessions` INT DEFAULT 1,
    `start_date` DATE,
    `end_date` DATE,
    `status` ENUM('proposed', 'approved', 'in_progress', 'completed', 'cancelled') DEFAULT 'proposed',
    `notes` TEXT,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE CASCADE,
    FOREIGN KEY (`dentist_id`) REFERENCES `dentists` (`id`) ON DELETE CASCADE
);

-- Treatment plan items (steps/procedures)
CREATE TABLE IF NOT EXISTS `treatment_plan_items` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `plan_id` INT NOT NULL,
    `treatment_id` INT,
    `step_number` INT NOT NULL,
    `title` VARCHAR(255) NOT NULL,
    `description` TEXT,
    `cost` DECIMAL(10,2) DEFAULT 0.00,
    `status` ENUM('pending', 'in_progress', 'completed', 'skipped') DEFAULT 'pending',
    `completed_at` TIMESTAMP NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`plan_id`) REFERENCES `treatment_plans` (`id`) ON DELETE CASCADE,
    FOREIGN KEY (`treatment_id`) REFERENCES `treatments` (`id`) ON DELETE SET NULL
);

-- =====================================================
-- SCHEDULE MANAGEMENT (Dentist App)
-- =====================================================

-- Dentist custom schedule overrides
CREATE TABLE IF NOT EXISTS `dentist_schedules` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `dentist_id` INT NOT NULL,
    `schedule_date` DATE NOT NULL,
    `start_time` TIME NOT NULL,
    `end_time` TIME NOT NULL,
    `is_available` ENUM('yes', 'no') DEFAULT 'yes',
    `reason` VARCHAR(255),
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`dentist_id`) REFERENCES `dentists` (`id`) ON DELETE CASCADE,
    UNIQUE KEY `unique_dentist_date_time` (`dentist_id`, `schedule_date`, `start_time`)
);

-- =====================================================
-- ONLINE PAYMENTS (Patient App)
-- =====================================================

-- Payment transactions
CREATE TABLE IF NOT EXISTS `payment_transactions` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `payment_id` INT NOT NULL,
    `transaction_id` VARCHAR(100),
    `gateway` ENUM('stripe', 'jazzcash', 'easypaisa', 'paypal', 'cod') NOT NULL,
    `amount` DECIMAL(10,2) NOT NULL,
    `currency` VARCHAR(3) DEFAULT 'PKR',
    `status` ENUM('pending', 'processing', 'completed', 'failed', 'refunded') DEFAULT 'pending',
    `gateway_response` JSON,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`payment_id`) REFERENCES `payments` (`id`) ON DELETE CASCADE
);

-- =====================================================
-- PUSH NOTIFICATIONS
-- =====================================================

-- Push notification logs
CREATE TABLE IF NOT EXISTS `push_notifications` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NOT NULL,
    `title` VARCHAR(255) NOT NULL,
    `body` TEXT NOT NULL,
    `data` JSON,
    `status` ENUM('sent', 'delivered', 'failed') DEFAULT 'sent',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
);

-- =====================================================
-- TREATMENT NOTES (Dentist App)
-- =====================================================

-- Detailed treatment notes
CREATE TABLE IF NOT EXISTS `treatment_notes` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `appointment_id` INT NOT NULL,
    `dentist_id` INT NOT NULL,
    `patient_id` INT NOT NULL,
    `title` VARCHAR(255) NOT NULL,
    `notes` TEXT,
    `diagnosis` TEXT,
    `procedure_done` TEXT,
    `medications_prescribed` TEXT,
    `follow_up_date` DATE,
    `follow_up_notes` TEXT,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`appointment_id`) REFERENCES `appointments` (`id`) ON DELETE CASCADE,
    FOREIGN KEY (`dentist_id`) REFERENCES `dentists` (`id`) ON DELETE CASCADE,
    FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE CASCADE
);

-- =====================================================
-- MEDICAL RECORDS UPLOADS (Both Apps)
-- =====================================================

-- Medical file uploads
CREATE TABLE IF NOT EXISTS `medical_files` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `patient_id` INT NOT NULL,
    `uploaded_by` INT NOT NULL,
    `upload_type` ENUM('xray', 'report', 'prescription', 'image', 'document', 'other') DEFAULT 'document',
    `title` VARCHAR(255) NOT NULL,
    `description` TEXT,
    `file_path` VARCHAR(255) NOT NULL,
    `file_name` VARCHAR(255) NOT NULL,
    `file_size` INT DEFAULT 0,
    `mime_type` VARCHAR(100),
    `status` ENUM('active', 'archived') DEFAULT 'active',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE CASCADE,
    FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`id`) ON DELETE CASCADE
);

-- =====================================================
-- SEED DATA
-- =====================================================

-- Create a sample dentist user for testing
INSERT INTO `users` (`role_id`, `name`, `email`, `phone`, `password`, `status`) VALUES
(3, 'Dr. Usman Ali', 'usman@grin.com', '+92 300 1112233', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.esc7hTuiGEs8q.N2', 'active')
ON DUPLICATE KEY UPDATE `email` = `email`;

INSERT INTO `dentists` (`user_id`, `specialization`, `qualification`, `experience_years`, `consultation_fee`, `bio`)
SELECT `id`, 'Orthodontics', 'BDS, MDS Orthodontics', 8, 3000.00, 'Specialist in braces and aligners'
FROM `users` WHERE `email` = 'usman@grin.com'
AND NOT EXISTS (SELECT 1 FROM `dentists` WHERE `user_id` = (SELECT `id` FROM `users` WHERE `email` = 'usman@grin.com'));

-- Create a sample patient user for testing
INSERT INTO `users` (`role_id`, `name`, `email`, `phone`, `password`, `status`) VALUES
(4, 'Ahmed Khan', 'ahmed@email.com', '+92 321 4567890', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.esc7hTuiGEs8q.N2', 'active')
ON DUPLICATE KEY UPDATE `email` = `email`;

INSERT INTO `patients` (`user_id`, `gender`, `date_of_birth`, `address`)
SELECT `id`, 'male', '1995-03-15', 'Lahore, Pakistan'
FROM `users` WHERE `email` = 'ahmed@email.com'
AND NOT EXISTS (SELECT 1 FROM `patients` WHERE `user_id` = (SELECT `id` FROM `users` WHERE `email` = 'ahmed@email.com'));

-- Sample appointments
INSERT INTO `appointments` (`patient_id`, `dentist_id`, `treatment_id`, `appointment_date`, `start_time`, `end_time`, `status`, `created_by`)
SELECT p.id, d.id, t.id, CURDATE(), '10:00', '11:00', 'confirmed', u.id
FROM patients p, dentists d, treatments t, users u
WHERE p.user_id = (SELECT id FROM users WHERE email = 'ahmed@email.com')
AND d.user_id = (SELECT id FROM users WHERE email = 'usman@grin.com')
AND t.name = 'Teeth Cleaning'
AND u.email = 'admin@grin.com'
LIMIT 1;
