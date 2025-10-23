-- ============================================
-- Enrollment Fix Verification Script
-- ============================================

-- Check if courses table exists and has data
SELECT 'Courses Table Check' as test_name;
SELECT COUNT(*) as course_count FROM courses;
SELECT * FROM courses ORDER BY id;

-- Check if enrollments table exists
SELECT 'Enrollments Table Check' as test_name;
DESCRIBE enrollments;

-- Check foreign key constraints
SELECT 'Foreign Key Constraints Check' as test_name;
SELECT 
    CONSTRAINT_NAME,
    TABLE_NAME,
    COLUMN_NAME,
    REFERENCED_TABLE_NAME,
    REFERENCED_COLUMN_NAME
FROM information_schema.KEY_COLUMN_USAGE 
WHERE REFERENCED_TABLE_SCHEMA = DATABASE()
AND TABLE_NAME = 'enrollments';

-- Check if users exist
SELECT 'Users Check' as test_name;
SELECT id, name, email, role FROM users WHERE role = 'student' LIMIT 5;

-- Verify courses exist with correct IDs
SELECT 'Course ID Verification' as test_name;
SELECT 
    CASE 
        WHEN EXISTS(SELECT 1 FROM courses WHERE id = 1) THEN 'EXISTS' 
        ELSE 'MISSING' 
    END as course_1_status,
    CASE 
        WHEN EXISTS(SELECT 1 FROM courses WHERE id = 2) THEN 'EXISTS' 
        ELSE 'MISSING' 
    END as course_2_status,
    CASE 
        WHEN EXISTS(SELECT 1 FROM courses WHERE id = 3) THEN 'EXISTS' 
        ELSE 'MISSING' 
    END as course_3_status,
    CASE 
        WHEN EXISTS(SELECT 1 FROM courses WHERE id = 4) THEN 'EXISTS' 
        ELSE 'MISSING' 
    END as course_4_status;

-- Check current enrollments
SELECT 'Current Enrollments' as test_name;
SELECT 
    e.id,
    e.user_id,
    u.name as student_name,
    e.course_id,
    c.title as course_title,
    e.enrollment_date,
    e.status
FROM enrollments e
LEFT JOIN users u ON e.user_id = u.id
LEFT JOIN courses c ON e.course_id = c.id
ORDER BY e.enrollment_date DESC;

-- ============================================
-- Fix Commands (run if needed)
-- ============================================

-- If courses are missing, insert them:
/*
INSERT IGNORE INTO courses (id, title, description, created_at, updated_at) VALUES
(1, 'Introduction to Programming', 'Learn the fundamentals of programming with Python', NOW(), NOW()),
(2, 'Web Development Basics', 'HTML, CSS, and JavaScript fundamentals', NOW(), NOW()),
(3, 'Database Management', 'SQL and database design principles', NOW(), NOW()),
(4, 'Data Structures & Algorithms', 'Advanced programming concepts and problem solving', NOW(), NOW());
*/

-- If you need to clear all enrollments (for testing):
/*
DELETE FROM enrollments WHERE id > 0;
*/

-- If you need to reset auto increment:
/*
ALTER TABLE enrollments AUTO_INCREMENT = 1;
*/
