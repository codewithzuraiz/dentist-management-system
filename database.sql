-- MySQL Database Schema for Grin Dental Management System
-- Created: 2026-08-05

-- Create database if not exists
CREATE DATABASE IF NOT EXISTS `grin_dental` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE `grin_dental`;

-- =====================================================
-- USER & AUTHENTICATION TABLES
-- =====================================================

-- Roles table for user roles
CREATE TABLE IF NOT EXISTS `roles` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(50) NOT NULL UNIQUE,
    `description` VARCHAR(255),
    `status` ENUM('active', 'inactive') DEFAULT 'active',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Users table for authentication
CREATE TABLE IF NOT EXISTS `users` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `role_id` INT NOT NULL,
    `name` VARCHAR(100) NOT NULL,
    `email` VARCHAR(100) NOT NULL UNIQUE,
    `phone` VARCHAR(20),
    `password` VARCHAR(255) NOT NULL,
    `profile_image` VARCHAR(255),
    `status` ENUM('active', 'inactive', 'suspended') DEFAULT 'active',
    `last_login` TIMESTAMP NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE RESTRICT
);

-- Permissions table for granular permissions
CREATE TABLE IF NOT EXISTS `permissions` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(50) NOT NULL UNIQUE,
    `description` VARCHAR(255),
    `module` VARCHAR(50) NOT NULL,
    `action` VARCHAR(20) NOT NULL,
    `status` ENUM('active', 'inactive') DEFAULT 'active',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Role permissions table for many-to-many relationship
CREATE TABLE IF NOT EXISTS `role_permissions` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `role_id` INT NOT NULL,
    `permission_id` INT NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE,
    FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
    UNIQUE KEY `unique_role_permission` (`role_id`, `permission_id`)
);

-- =====================================================
-- DENTAL CLINIC CORE TABLES
-- =====================================================

-- Dentists table
CREATE TABLE IF NOT EXISTS `dentists` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NOT NULL UNIQUE,
    `specialization` VARCHAR(100) NOT NULL,
    `qualification` TEXT,
    `experience_years` INT DEFAULT 0,
    `consultation_fee` DECIMAL(10,2) DEFAULT 0.00,
    `bio` TEXT,
    `profile_image` VARCHAR(255),
    `status` ENUM('active', 'inactive', 'on_leave') DEFAULT 'active',
    `working_days` VARCHAR(50),
    `working_start_time` TIME,
    `working_end_time` TIME,
    `break_start_time` TIME,
    `break_end_time` TIME,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
);

-- Patients table
CREATE TABLE IF NOT EXISTS `patients` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT UNIQUE,
    `gender` ENUM('male', 'female', 'other') DEFAULT 'male',
    `date_of_birth` DATE,
    `blood_group` VARCHAR(5),
    `address` TEXT,
    `emergency_contact_name` VARCHAR(100),
    `emergency_contact_phone` VARCHAR(20),
    `allergies` TEXT,
    `medical_conditions` TEXT,
    `profile_image` VARCHAR(255),
    `status` ENUM('active', 'inactive') DEFAULT 'active',
    `registration_date` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
);

-- Treatments table
CREATE TABLE IF NOT EXISTS `treatments` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL UNIQUE,
    `description` TEXT,
    `cost` DECIMAL(10,2) NOT NULL,
    `duration_minutes` INT,
    `required_equipment` TEXT,
    `status` ENUM('active', 'inactive') DEFAULT 'active',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- =====================================================
-- APPOINTMENT SYSTEM
-- =====================================================

-- Appointments table
CREATE TABLE IF NOT EXISTS `appointments` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `patient_id` INT NOT NULL,
    `dentist_id` INT NOT NULL,
    `treatment_id` INT NOT NULL,
    `appointment_date` DATE NOT NULL,
    `start_time` TIME NOT NULL,
    `end_time` TIME NOT NULL,
    `status` ENUM('pending', 'confirmed', 'completed', 'cancelled', 'rejected', 'no_show') DEFAULT 'pending',
    `notes` TEXT,
    `created_by` INT NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE CASCADE,
    FOREIGN KEY (`dentist_id`) REFERENCES `dentists` (`id`) ON DELETE CASCADE,
    FOREIGN KEY (`treatment_id`) REFERENCES `treatments` (`id`) ON DELETE RESTRICT,
    FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE RESTRICT
);

-- =====================================================
-- BILLING & INVOICES
-- =====================================================

-- Invoices table
CREATE TABLE IF NOT EXISTS `invoices` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `invoice_number` VARCHAR(20) NOT NULL UNIQUE,
    `patient_id` INT NOT NULL,
    `dentist_id` INT NOT NULL,
    `appointment_id` INT,
    `subtotal` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `tax_rate` DECIMAL(5,2) DEFAULT 0.00,
    `tax_amount` DECIMAL(10,2) GENERATED ALWAYS AS (`subtotal` * `tax_rate` / 100) STORED,
    `discount` DECIMAL(10,2) DEFAULT 0.00,
    `total_amount` DECIMAL(10,2) GENERATED ALWAYS AS (`subtotal` + `tax_amount` - `discount`) STORED,
    `paid_amount` DECIMAL(10,2) DEFAULT 0.00,
    `payment_date` TIMESTAMP NULL,
    `due_date` DATE,
    `status` ENUM('paid', 'partially_paid', 'unpaid', 'overdue', 'cancelled') DEFAULT 'unpaid',
    `notes` TEXT,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE CASCADE,
    FOREIGN KEY (`dentist_id`) REFERENCES `dentists` (`id`) ON DELETE CASCADE,
    FOREIGN KEY (`appointment_id`) REFERENCES `appointments` (`id`) ON DELETE SET NULL
);

-- Invoice items table
CREATE TABLE IF NOT EXISTS `invoice_items` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `invoice_id` INT NOT NULL,
    `treatment_id` INT NOT NULL,
    `quantity` INT DEFAULT 1,
    `unit_price` DECIMAL(10,2) NOT NULL,
    `discount` DECIMAL(10,2) DEFAULT 0.00,
    `subtotal` DECIMAL(10,2) GENERATED ALWAYS AS (`unit_price` * `quantity` - `discount`) STORED,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`) ON DELETE CASCADE,
    FOREIGN KEY (`treatment_id`) REFERENCES `treatments` (`id`) ON DELETE RESTRICT
);

-- Payments table
CREATE TABLE IF NOT EXISTS `payments` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `invoice_id` INT NOT NULL,
    `patient_id` INT NOT NULL,
    `amount` DECIMAL(10,2) NOT NULL,
    `payment_method` ENUM('cash', 'card', 'bank_transfer', 'jazzcash', 'easypaisa') DEFAULT 'cash',
    `transaction_reference` VARCHAR(100),
    `payment_date` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `recorded_by` INT NOT NULL,
    `notes` TEXT,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`) ON DELETE CASCADE,
    FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE CASCADE,
    FOREIGN KEY (`recorded_by`) REFERENCES `users` (`id`) ON DELETE RESTRICT
);

-- =====================================================
-- PRESCRIPTIONS
-- =====================================================

-- Prescriptions table
CREATE TABLE IF NOT EXISTS `prescriptions` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `prescription_number` VARCHAR(20) NOT NULL UNIQUE,
    `patient_id` INT NOT NULL,
    `dentist_id` INT NOT NULL,
    `appointment_id` INT,
    `issued_date` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `expiry_date` DATE,
    `notes` TEXT,
    `status` ENUM('active', 'completed', 'cancelled') DEFAULT 'active',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE CASCADE,
    FOREIGN KEY (`dentist_id`) REFERENCES `dentists` (`id`) ON DELETE CASCADE,
    FOREIGN KEY (`appointment_id`) REFERENCES `appointments` (`id`) ON DELETE SET NULL
);

-- Prescription medicines table
CREATE TABLE IF NOT EXISTS `prescription_medicines` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `prescription_id` INT NOT NULL,
    `medicine_name` VARCHAR(100) NOT NULL,
    `dosage` VARCHAR(50) NOT NULL,
    `frequency` VARCHAR(50) NOT NULL,
    `duration` VARCHAR(50) NOT NULL,
    `instructions` TEXT,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`prescription_id`) REFERENCES `prescriptions` (`id`) ON DELETE CASCADE
);

-- =====================================================
-- MEDICAL RECORDS
-- =====================================================

-- Medical records table
CREATE TABLE IF NOT EXISTS `medical_records` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `patient_id` INT NOT NULL,
    `dentist_id` INT NOT NULL,
    `appointment_id` INT,
    `record_type` ENUM('consultation', 'xray', 'image', 'report', 'treatment_note') DEFAULT 'consultation',
    `title` VARCHAR(255) NOT NULL,
    `description` TEXT,
    `file_path` VARCHAR(255),
    `file_type` VARCHAR(50),
    `file_size` INT,
    `uploaded_by` INT NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE CASCADE,
    FOREIGN KEY (`dentist_id`) REFERENCES `dentists` (`id`) ON DELETE CASCADE,
    FOREIGN KEY (`appointment_id`) REFERENCES `appointments` (`id`) ON DELETE SET NULL,
    FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`id`) ON DELETE RESTRICT
);

-- =====================================================
-- NOTIFICATIONS
-- =====================================================

-- Notifications table
CREATE TABLE IF NOT EXISTS `notifications` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NOT NULL,
    `type` VARCHAR(50) NOT NULL,
    `title` VARCHAR(255) NOT NULL,
    `message` TEXT NOT NULL,
    `related_entity_type` VARCHAR(50),
    `related_entity_id` INT,
    `is_read` ENUM('yes', 'no') DEFAULT 'no',
    `sent` ENUM('yes', 'no') DEFAULT 'no',
    `sent_at` TIMESTAMP NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
);

-- =====================================================
-- AUDIT LOGS
-- =====================================================

-- Audit logs table
CREATE TABLE IF NOT EXISTS `audit_logs` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT,
    `action` VARCHAR(100) NOT NULL,
    `module` VARCHAR(50) NOT NULL,
    `entity_type` VARCHAR(50) NOT NULL,
    `entity_id` INT,
    `description` TEXT,
    `old_values` JSON,
    `new_values` JSON,
    `ip_address` VARCHAR(45),
    `user_agent` TEXT,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
);

-- =====================================================
-- SYSTEM SETTINGS
-- =====================================================

-- System settings table
CREATE TABLE IF NOT EXISTS `system_settings` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `clinic_name` VARCHAR(100) NOT NULL,
    `clinic_logo` VARCHAR(255),
    `clinic_address` TEXT,
    `clinic_phone` VARCHAR(20),
    `clinic_email` VARCHAR(100),
    `clinic_website` VARCHAR(100),
    `currency` VARCHAR(3) DEFAULT 'PKR',
    `timezone` VARCHAR(50) DEFAULT 'Asia/Karachi',
    `tax_rate` DECIMAL(5,2) DEFAULT 0.00,
    `appointment_slot_duration` INT DEFAULT 30,
    `max_daily_appointments` INT DEFAULT 20,
    `appointment_reminder_hours` INT DEFAULT 24,
    `email_enabled` ENUM('yes', 'no') DEFAULT 'yes',
    `sms_enabled` ENUM('yes', 'no') DEFAULT 'no',
    `whatsapp_enabled` ENUM('yes', 'no') DEFAULT 'no',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- =====================================================
-- SEED DATA
-- =====================================================

-- Insert default roles
INSERT INTO `roles` (`name`, `description`) VALUES
('super_admin', 'Super Administrator with full access'),
('admin', 'Administrator with management access'),
('dentist', 'Dental professional with clinical access'),
('receptionist', 'Front desk staff with limited access');

-- Insert default permissions
INSERT INTO `permissions` (`name`, `description`, `module`, `action`) VALUES
-- Patient permissions
('patients.view', 'View patients', 'patients', 'view'),
('patients.create', 'Add patients', 'patients', 'create'),
('patients.update', 'Edit patients', 'patients', 'update'),
('patients.delete', 'Delete patients', 'patients', 'delete'),

-- Dentist permissions
('dentists.view', 'View dentists', 'dentists', 'view'),
('dentists.create', 'Add dentists', 'dentists', 'create'),
('dentists.update', 'Edit dentists', 'dentists', 'update'),
('dentists.delete', 'Delete dentists', 'dentists', 'delete'),

-- Appointment permissions
('appointments.view', 'View appointments', 'appointments', 'view'),
('appointments.create', 'Create appointments', 'appointments', 'create'),
('appointments.update', 'Update appointments', 'appointments', 'update'),
('appointments.delete', 'Delete appointments', 'appointments', 'delete'),

-- Treatment permissions
('treatments.view', 'View treatments', 'treatments', 'view'),
('treatments.create', 'Add treatments', 'treatments', 'create'),
('treatments.update', 'Update treatments', 'treatments', 'update'),
('treatments.delete', 'Delete treatments', 'treatments', 'delete'),

-- Billing permissions
('billing.view', 'View billing', 'billing', 'view'),
('billing.create', 'Create bills', 'billing', 'create'),
('billing.update', 'Update bills', 'billing', 'update'),
('billing.delete', 'Delete bills', 'billing', 'delete'),
('payments.view', 'View payments', 'payments', 'view'),
('payments.create', 'Record payments', 'payments', 'create'),

-- Prescription permissions
('prescriptions.view', 'View prescriptions', 'prescriptions', 'view'),
('prescriptions.create', 'Create prescriptions', 'prescriptions', 'create'),
('prescriptions.update', 'Update prescriptions', 'prescriptions', 'update'),
('prescriptions.delete', 'Delete prescriptions', 'prescriptions', 'delete'),

-- Medical records permissions
('medical_records.view', 'View medical records', 'medical_records', 'view'),
('medical_records.create', 'Upload medical records', 'medical_records', 'create'),
('medical_records.update', 'Update medical records', 'medical_records', 'update'),
('medical_records.delete', 'Delete medical records', 'medical_records', 'delete'),

-- Reports permissions
('reports.view', 'View reports', 'reports', 'view'),
('reports.generate', 'Generate reports', 'reports', 'generate'),

-- Settings permissions
('settings.view', 'View settings', 'settings', 'view'),
('settings.update', 'Update settings', 'settings', 'update'),

-- User management permissions
('users.view', 'View users', 'users', 'view'),
('users.create', 'Create users', 'users', 'create'),
('users.update', 'Update users', 'users', 'update'),
('users.delete', 'Delete users', 'users', 'delete'),

-- Role permissions
('roles.view', 'View roles', 'roles', 'view'),
('roles.create', 'Create roles', 'roles', 'create'),
('roles.update', 'Update roles', 'roles', 'update'),
('roles.delete', 'Delete roles', 'roles', 'delete');

-- Insert default admin user
INSERT INTO `users` (`role_id`, `name`, `email`, `phone`, `password`, `status`) VALUES
(1, 'Super Admin', 'admin@grin.com', '+92 300 1234567', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.esc7hTuiGEs8q.N2', 'active');

-- Insert default dentist user
INSERT INTO `users` (`role_id`, `name`, `email`, `phone`, `password`, `status`) VALUES
(3, 'Dr. Ahmed Khan', 'ahmed@grin.com', '+92 300 9876543', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.esc7hTuiGEs8q.N2', 'active');

-- Create role permissions for super admin
INSERT INTO `role_permissions` (`role_id`, `permission_id`)
SELECT `r`.`id`, `p`.`id`
FROM `roles` `r`, `permissions` `p`
WHERE `r`.`name` = 'super_admin';

-- Create role permissions for admin
INSERT INTO `role_permissions` (`role_id`, `permission_id`)
SELECT `r`.`id`, `p`.`id`
FROM `roles` `r`, `permissions` `p`
WHERE `r`.`name` = 'admin' AND (
    `p`.`module` = 'patients' AND `p`.`action` IN ('view', 'create', 'update')
    OR `p`.`module` = 'dentists' AND `p`.`action` = 'view'
    OR `p`.`module` = 'appointments' AND `p`.`action` IN ('view', 'create', 'update')
    OR `p`.`module` = 'treatments' AND `p`.`action` = 'view'
    OR `p`.`module` = 'billing' AND `p`.`action` IN ('view', 'create', 'update')
    OR `p`.`module` = 'payments' AND `p`.`action` IN ('view', 'create')
    OR `p`.`module` = 'prescriptions' AND `p`.`action` IN ('view', 'create')
    OR `p`.`module` = 'medical_records' AND `p`.`action` = 'view'
    OR `p`.`module` = 'reports' AND `p`.`action` = 'view'
    OR `p`.`module` = 'settings' AND `p`.`action` = 'view'
);

-- Create role permissions for dentist
INSERT INTO `role_permissions` (`role_id`, `permission_id`)
SELECT `r`.`id`, `p`.`id`
FROM `roles` `r`, `permissions` `p`
WHERE `r`.`name` = 'dentist' AND (
    `p`.`module` = 'appointments' AND `p`.`action` IN ('view', 'create', 'update')
    OR `p`.`module` = 'treatments' AND `p`.`action` = 'view'
    OR `p`.`module` = 'prescriptions' AND `p`.`action` IN ('view', 'create', 'update')
    OR `p`.`module` = 'medical_records' AND `p`.`action` IN ('view', 'create', 'update')
    OR `p`.`module` = 'patients' AND `p`.`action` = 'view'
);

-- Create role permissions for receptionist
INSERT INTO `role_permissions` (`role_id`, `permission_id`)
SELECT `r`.`id`, `p`.`id`
FROM `roles` `r`, `permissions` `p`
WHERE `r`.`name` = 'receptionist' AND (
    `p`.`module` = 'patients' AND `p`.`action` IN ('view', 'create', 'update')
    OR `p`.`module` = 'appointments' AND `p`.`action` IN ('view', 'create', 'update')
    OR `p`.`module` = 'treatments' AND `p`.`action` = 'view'
    OR `p`.`module` = 'billing' AND `p`.`action` IN ('view', 'create')
    OR `p`.`module` = 'payments' AND `p`.`action` = 'create'
    OR `p`.`module` = 'prescriptions' AND `p`.`action` = 'create'
);

-- Insert default system settings
INSERT INTO `system_settings` (`clinic_name`, `clinic_address`, `clinic_phone`, `clinic_email`, `currency`, `tax_rate`) 
VALUES (
    'Grin Dental Clinic',
    'Karachi, Pakistan',
    '+92 300 1234567',
    'info@grin.com',
    'PKR',
    18.00
);

-- Create super admin user record for the dentist
INSERT INTO `dentists` (`user_id`, `specialization`, `qualification`, `experience_years`, `consultation_fee`, `bio`) 
SELECT `id`, 'General Dentistry', 'MBBS, MDS', 10, 2500.00, 'Experienced dentist specializing in general dental care'
FROM `users`
WHERE `email` = 'ahmed@grin.com';

-- Seed treatments
INSERT INTO `treatments` (`name`, `description`, `cost`, `duration_minutes`, `required_equipment`) VALUES
('Teeth Cleaning', 'Professional dental cleaning and scaling', 2000.00, 60, 'Scaler, Dental Mirror'),
('Root Canal', 'Endodontic treatment for infected pulp', 8000.00, 90, 'Hand pieces, Files, Irrigation'),
('Dental Filling', 'Composite resin filling for cavities', 3000.00, 45, 'Dental Amalgam, Condenser'),
('Teeth Whitening', 'Professional teeth whitening treatment', 15000.00, 120, 'Whitening gel, LED light'),
('Tooth Extraction', 'Surgical tooth removal', 5000.00, 60, 'Forceps, Surgical blades'),
('Dental Crown', 'Ceramic crown preparation and placement', 12000.00, 120, 'Crown material, CNC machine'),
('Cosmetic Dentistry', 'Complete smile makeover', 25000.00, 180, 'Composite, Veneers');

-- Seed sample patient
INSERT INTO `users` (`role_id`, `name`, `email`, `phone`, `password`, `status`) VALUES
(4, 'Sarah Johnson', 'sarah@email.com', '+1 555 0123', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.esc7hTuiGEs8q.N2', 'active');

INSERT INTO `patients` (`user_id`, `gender`, `date_of_birth`, `address`, `profile_image`) 
SELECT `id`, 'female', '1990-05-15', 'New York, USA', NULL
FROM `users`
WHERE `email` = 'sarah@email.com';
