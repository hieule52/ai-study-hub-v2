-- =============================================
-- AI Study Hub LMS - Migration V2
-- Cấu trúc khóa học nâng cao, Video Security,
-- Learning Paths (Lộ trình học)
-- =============================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

-- =============================================
-- 1. COURSE CATEGORIES (Danh mục khóa học)
-- =============================================
CREATE TABLE IF NOT EXISTS `course_categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL UNIQUE,
  `icon` varchar(50) DEFAULT NULL COMMENT 'Font Awesome icon class',
  `parent_id` int(11) DEFAULT NULL,
  `order_index` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`parent_id`) REFERENCES `course_categories`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Seed default categories
INSERT INTO `course_categories` (`name`, `slug`, `icon`, `order_index`) VALUES
('Lập trình', 'lap-trinh', 'fa-code', 1),
('Khoa học dữ liệu', 'khoa-hoc-du-lieu', 'fa-chart-bar', 2),
('Trí tuệ nhân tạo', 'tri-tue-nhan-tao', 'fa-robot', 3),
('Thiết kế', 'thiet-ke', 'fa-paint-brush', 4),
('Ngoại ngữ', 'ngoai-ngu', 'fa-language', 5),
('Toán học', 'toan-hoc', 'fa-calculator', 6),
('Kinh doanh', 'kinh-doanh', 'fa-briefcase', 7),
('Khác', 'khac', 'fa-folder', 99);

-- =============================================
-- 2. ALTER COURSES - Thêm category, level
-- =============================================
ALTER TABLE `courses`
  ADD COLUMN `category_id` int(11) DEFAULT NULL AFTER `teacher_id`,
  ADD COLUMN `level` enum('beginner','intermediate','advanced') NOT NULL DEFAULT 'beginner' AFTER `category_id`,
  ADD COLUMN `estimated_duration` int(11) NOT NULL DEFAULT 0 COMMENT 'Tổng thời lượng ước tính (phút)' AFTER `level`,
  ADD FOREIGN KEY (`category_id`) REFERENCES `course_categories`(`id`) ON DELETE SET NULL;

-- =============================================
-- 3. ALTER LESSONS - Thêm content_type, video metadata
-- =============================================
ALTER TABLE `lessons`
  ADD COLUMN `content_type` enum('video','text','quiz') NOT NULL DEFAULT 'video' AFTER `title`,
  ADD COLUMN `duration` int(11) NOT NULL DEFAULT 0 COMMENT 'Thời lượng video (giây)' AFTER `video_url`,
  ADD COLUMN `video_filename` varchar(255) DEFAULT NULL COMMENT 'Tên file video thật trên disk (UUID)' AFTER `duration`,
  ADD COLUMN `video_size` bigint(20) NOT NULL DEFAULT 0 COMMENT 'Kích thước file video (bytes)' AFTER `video_filename`;

-- =============================================
-- 4. VIDEO TOKENS - Token tạm thời để stream video
-- Cơ chế: Signed URL có thời hạn (4h mặc định)
-- =============================================
CREATE TABLE IF NOT EXISTS `video_tokens` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `token` varchar(64) NOT NULL,
  `user_id` int(11) NOT NULL,
  `lesson_id` int(11) NOT NULL,
  `expires_at` timestamp NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_token` (`token`),
  KEY `idx_expires` (`expires_at`),
  KEY `idx_user_lesson` (`user_id`, `lesson_id`),
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`lesson_id`) REFERENCES `lessons`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- 5. LEARNING PATHS (Lộ trình học)
-- =============================================
CREATE TABLE IF NOT EXISTS `learning_paths` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `description` text,
  `thumbnail` varchar(255) DEFAULT NULL,
  `difficulty` enum('beginner','intermediate','advanced') NOT NULL DEFAULT 'beginner',
  `estimated_hours` int(11) NOT NULL DEFAULT 0,
  `created_by` int(11) NOT NULL,
  `is_published` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`created_by`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- 6. LEARNING PATH COURSES (Khóa học trong lộ trình)
-- =============================================
CREATE TABLE IF NOT EXISTS `learning_path_courses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `path_id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `order_index` int(11) NOT NULL DEFAULT 0,
  `is_required` tinyint(1) NOT NULL DEFAULT 1 COMMENT '1=bắt buộc, 0=tùy chọn',
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_path_course` (`path_id`, `course_id`),
  FOREIGN KEY (`path_id`) REFERENCES `learning_paths`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`course_id`) REFERENCES `courses`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- 7. LEARNING PATH ENROLLMENTS (Đăng ký lộ trình)
-- =============================================
CREATE TABLE IF NOT EXISTS `learning_path_enrollments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `path_id` int(11) NOT NULL,
  `progress_percent` int(11) NOT NULL DEFAULT 0,
  `enrolled_at` timestamp NULL DEFAULT current_timestamp(),
  `completed_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_user_path` (`user_id`, `path_id`),
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`path_id`) REFERENCES `learning_paths`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

COMMIT;
