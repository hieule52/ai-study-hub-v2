-- Migration v4: Admin Moderation & Approval Notification System

-- 1. Modify courses table status enum to support pending_reapproval
ALTER TABLE `courses` MODIFY COLUMN `status` ENUM('draft','pending','approved','published','hidden','pending_reapproval') DEFAULT 'draft';

-- 2. Unified change logs table to record chapter, lesson, quiz, and context modifications
CREATE TABLE IF NOT EXISTS `course_change_logs` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `course_id` INT NOT NULL,
  `entity_type` VARCHAR(50) NOT NULL, -- 'course', 'chapter', 'lesson', 'quiz'
  `entity_id` INT NOT NULL,
  `action_type` VARCHAR(50) NOT NULL, -- 'create', 'update', 'delete'
  `changed_fields` TEXT NOT NULL, -- JSON array of modified keys
  `old_snapshot` LONGTEXT NULL, -- JSON snapshot of old state
  `new_snapshot` LONGTEXT NULL, -- JSON snapshot of new state
  `performed_by` INT NOT NULL, -- user_id of actor
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  KEY `idx_course_changes` (`course_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- 3. Dedicated admin notifications table
CREATE TABLE IF NOT EXISTS `admin_notifications` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `course_id` INT NOT NULL,
  `type` VARCHAR(50) NOT NULL, -- 'pending_approval', 'critical_update', 'non_critical_update'
  `title` VARCHAR(255) NOT NULL,
  `message` TEXT NOT NULL,
  `data` LONGTEXT NULL, -- JSON payload of the modification context
  `is_read` TINYINT DEFAULT 0,
  `priority` VARCHAR(20) DEFAULT 'medium', -- 'high', 'medium', 'low'
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  KEY `idx_admin_notifs_read` (`is_read`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
