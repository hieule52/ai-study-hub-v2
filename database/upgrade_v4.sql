-- Upgrade script to support video/text progress tracking, quiz passing score, and course completion status

ALTER TABLE lesson_progress ADD COLUMN video_progress INT NOT NULL DEFAULT 0;
ALTER TABLE lesson_progress ADD COLUMN text_progress INT NOT NULL DEFAULT 0;

ALTER TABLE quizzes ADD COLUMN passing_score INT NOT NULL DEFAULT 80;

ALTER TABLE enrollments ADD COLUMN course_status VARCHAR(50) NOT NULL DEFAULT 'learning';
