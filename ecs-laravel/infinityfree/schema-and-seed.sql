-- ==========================================================================
-- Electronic Complaint System — full schema + sample data
-- Import this via phpMyAdmin on InfinityFree (no SSH/artisan available there).
-- Matches the migrations in database/migrations exactly.
-- ==========================================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- --------------------------------------------------------------------------
-- users  (Laravel default table + is_admin/phone/address from our migration)
-- --------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `is_admin` tinyint(1) NOT NULL DEFAULT 0,
  `phone` varchar(255) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Needed for Laravel Breeze's "Forgot Password" feature
CREATE TABLE IF NOT EXISTS `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------------------------
-- departments
-- --------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `departments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `contact_person` varchar(255) DEFAULT NULL,
  `contact_number` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------------------------
-- complaints
-- --------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `complaints` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `reference_number` varchar(255) NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `category` varchar(255) NOT NULL,
  `location` varchar(255) NOT NULL,
  `latitude` decimal(10,7) DEFAULT NULL,
  `longitude` decimal(10,7) DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'Pending',
  `admin_remarks` text,
  `assigned_to` varchar(255) DEFAULT NULL,
  `department_id` bigint unsigned DEFAULT NULL,
  `resolution_photo` varchar(255) DEFAULT NULL,
  `resolved_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `complaints_reference_number_unique` (`reference_number`),
  KEY `complaints_category_status_index` (`category`, `status`),
  KEY `complaints_user_id_foreign` (`user_id`),
  KEY `complaints_department_id_foreign` (`department_id`),
  CONSTRAINT `complaints_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `complaints_department_id_foreign` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------------------------
-- complaint_images
-- --------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `complaint_images` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `complaint_id` bigint unsigned NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `complaint_images_complaint_id_foreign` (`complaint_id`),
  CONSTRAINT `complaint_images_complaint_id_foreign` FOREIGN KEY (`complaint_id`) REFERENCES `complaints` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------------------------
-- complaint_updates  (audit trail)
-- --------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `complaint_updates` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `complaint_id` bigint unsigned NOT NULL,
  `status` varchar(255) NOT NULL,
  `remarks` text,
  `updated_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `complaint_updates_complaint_id_foreign` (`complaint_id`),
  KEY `complaint_updates_updated_by_foreign` (`updated_by`),
  CONSTRAINT `complaint_updates_complaint_id_foreign` FOREIGN KEY (`complaint_id`) REFERENCES `complaints` (`id`) ON DELETE CASCADE,
  CONSTRAINT `complaint_updates_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Laravel's own bookkeeping table so `php artisan migrate:status` (if you
-- ever get shell access later, e.g. after upgrading) doesn't try to re-run
-- these. Safe to leave out if you never plan to run artisan migrate here.
CREATE TABLE IF NOT EXISTS `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `migrations` (`migration`, `batch`) VALUES
('0001_01_01_000000_create_users_table', 1),
('2024_01_01_000001_add_profile_fields_to_users_table', 1),
('2024_01_01_000002_create_departments_table', 1),
('2024_01_01_000003_create_complaints_table', 1),
('2024_01_01_000004_create_complaint_images_table', 1),
('2024_01_01_000005_create_complaint_updates_table', 1);

SET FOREIGN_KEY_CHECKS = 1;

-- ==========================================================================
-- Seed data
-- ==========================================================================

-- Admin login:    admin@ecs.gov.ph / password
-- Resident login: juan@example.com / password
-- (bcrypt hash below is for the plaintext word "password")
INSERT INTO `users` (`name`, `email`, `is_admin`, `phone`, `address`, `email_verified_at`, `password`, `created_at`, `updated_at`)
VALUES
('Barangay Admin', 'admin@ecs.gov.ph', 1, NULL, NULL, NOW(), '$2b$10$N1muiOBjKuUnOKQ3DQD8xOrcrBgVzYYeIR/8U9HaKRDFgcGbgTKEy', NOW(), NOW()),
('Juan Dela Cruz', 'juan@example.com', 0, '09171234567', 'Purok 3, Barangay Sample', NOW(), '$2b$10$N1muiOBjKuUnOKQ3DQD8xOrcrBgVzYYeIR/8U9HaKRDFgcGbgTKEy', NOW(), NOW());

INSERT INTO `departments` (`name`, `contact_person`, `contact_number`, `created_at`, `updated_at`) VALUES
('Public Works & Infrastructure', 'Engr. Santos', '09170000001', NOW(), NOW()),
('Sanitation & Waste Management', 'Mr. Reyes', '09170000002', NOW(), NOW()),
('Barangay Tanod (Public Safety)', 'Tanod Chief Cruz', '09170000003', NOW(), NOW()),
('Drainage & Flood Control', 'Engr. Bautista', '09170000004', NOW(), NOW());

-- Sample complaints filed by Juan Dela Cruz (user id 2)
INSERT INTO `complaints` (`reference_number`, `user_id`, `title`, `description`, `category`, `location`, `status`, `admin_remarks`, `created_at`, `updated_at`) VALUES
('ECS-2026-000001', 2, 'Large pothole along Rizal Street', 'A deep pothole near the barangay hall is causing traffic and is dangerous at night.', 'Road', 'Rizal Street, near Barangay Hall', 'Pending', NULL, NOW(), NOW()),
('ECS-2026-000002', 2, 'Uncollected garbage for 3 days', 'Garbage bins along Mabini St. have not been collected since Monday and are starting to smell.', 'Garbage', 'Mabini Street, Purok 2', 'Under Review', NULL, NOW(), NOW()),
('ECS-2026-000003', 2, 'Broken street light at the basketball court', 'The street light near the covered court has been flickering and is now completely out.', 'Street Light', 'Barangay Covered Court', 'In Progress', NULL, NOW(), NOW()),
('ECS-2026-000004', 2, 'Flooding after heavy rain', 'The intersection floods knee-deep every time it rains hard due to a clogged drainage canal.', 'Drainage', 'Corner of Bonifacio and Luna St.', 'Resolved', NULL, NOW(), NOW()),
('ECS-2026-000005', 2, 'Stray dogs causing safety concerns', 'A pack of stray dogs has been chasing children walking to school in the morning.', 'Safety', 'Near Sample Elementary School', 'Rejected', 'Duplicate report / outside barangay jurisdiction.', NOW(), NOW());

-- Matching audit-trail rows so "Status History" isn't empty on each sample complaint
INSERT INTO `complaint_updates` (`complaint_id`, `status`, `remarks`, `updated_by`, `created_at`, `updated_at`)
SELECT id, status, 'Seeded sample record.', NULL, NOW(), NOW() FROM `complaints`;
