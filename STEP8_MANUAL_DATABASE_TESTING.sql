-- ============================================
-- Step 8: Manual Database Testing SQL Script
-- ============================================

-- Use this script to manually create test notifications for Step 8 testing
-- Replace [USER_ID] with the actual user ID you want to test with

-- ============================================
-- 1. Check current notifications for a user
-- ============================================
SELECT 
    id,
    user_id,
    message,
    is_read,
    created_at,
    CASE 
        WHEN is_read = 0 THEN 'Unread'
        ELSE 'Read'
    END as status
FROM notifications 
WHERE user_id = [USER_ID] 
ORDER BY created_at DESC;

-- ============================================
-- 2. Count unread notifications for a user
-- ============================================
SELECT COUNT(*) as unread_count 
FROM notifications 
WHERE user_id = [USER_ID] AND is_read = 0;

-- ============================================
-- 3. Create a single test notification
-- ============================================
INSERT INTO notifications (user_id, message, is_read, created_at) 
VALUES (
    [USER_ID], 
    'Test notification created manually for Step 8 testing', 
    0, 
    NOW()
);

-- ============================================
-- 4. Create multiple test notifications
-- ============================================
INSERT INTO notifications (user_id, message, is_read, created_at) VALUES
([USER_ID], 'Welcome to the notification system!', 0, NOW()),
([USER_ID], 'Your course enrollment has been confirmed', 0, DATE_SUB(NOW(), INTERVAL 5 MINUTE)),
([USER_ID], 'New material uploaded: Chapter1.pdf', 0, DATE_SUB(NOW(), INTERVAL 15 MINUTE)),
([USER_ID], 'Assignment deadline reminder', 0, DATE_SUB(NOW(), INTERVAL 1 HOUR)),
([USER_ID], 'This notification is already read', 1, DATE_SUB(NOW(), INTERVAL 2 HOUR));

-- ============================================
-- 5. Create course enrollment notification (realistic)
-- ============================================
INSERT INTO notifications (user_id, message, is_read, created_at) 
VALUES (
    [USER_ID], 
    'You have been successfully enrolled in ''Introduction to Programming''. Welcome to the course!', 
    0, 
    NOW()
);

-- ============================================
-- 6. Create material upload notification (realistic)
-- ============================================
INSERT INTO notifications (user_id, message, is_read, created_at) 
VALUES (
    [USER_ID], 
    'New material ''Lecture_Notes_Week1.pdf'' has been uploaded to your course ''Web Development Basics''. Check it out now!', 
    0, 
    NOW()
);

-- ============================================
-- 7. Create announcement notification (realistic)
-- ============================================
INSERT INTO notifications (user_id, message, is_read, created_at) 
VALUES (
    [USER_ID], 
    'New announcement posted: ''Exam Schedule Update''. Check it out in the announcements section!', 
    0, 
    NOW()
);

-- ============================================
-- 8. Create welcome notification (realistic)
-- ============================================
INSERT INTO notifications (user_id, message, is_read, created_at) 
VALUES (
    [USER_ID], 
    'Welcome to our Learning Management System! Start exploring courses and announcements.', 
    0, 
    NOW()
);

-- ============================================
-- 9. Mark specific notification as read (for testing)
-- ============================================
UPDATE notifications 
SET is_read = 1 
WHERE id = [NOTIFICATION_ID] AND user_id = [USER_ID];

-- ============================================
-- 10. Mark all notifications as read for a user
-- ============================================
UPDATE notifications 
SET is_read = 1 
WHERE user_id = [USER_ID];

-- ============================================
-- 11. Mark all notifications as unread for testing
-- ============================================
UPDATE notifications 
SET is_read = 0 
WHERE user_id = [USER_ID];

-- ============================================
-- 12. Delete test notifications (cleanup)
-- ============================================
DELETE FROM notifications 
WHERE user_id = [USER_ID] 
AND message LIKE '%test%' 
OR message LIKE '%Test%';

-- ============================================
-- 13. Get user information (to find USER_ID)
-- ============================================
SELECT id, name, email, role 
FROM users 
WHERE role = 'student' 
ORDER BY created_at DESC;

-- ============================================
-- 14. Verify notification system tables
-- ============================================
DESCRIBE notifications;

-- ============================================
-- 15. Check notification statistics
-- ============================================
SELECT 
    u.name,
    u.role,
    COUNT(n.id) as total_notifications,
    SUM(CASE WHEN n.is_read = 0 THEN 1 ELSE 0 END) as unread_count,
    SUM(CASE WHEN n.is_read = 1 THEN 1 ELSE 0 END) as read_count
FROM users u
LEFT JOIN notifications n ON u.id = n.user_id
WHERE u.role = 'student'
GROUP BY u.id, u.name, u.role
ORDER BY total_notifications DESC;

-- ============================================
-- STEP 8 TESTING INSTRUCTIONS:
-- ============================================

/*
1. FIND YOUR USER ID:
   - Run query #13 to see all users
   - Note the ID of the student account you want to test with
   - Replace [USER_ID] in the queries below with this ID

2. CREATE TEST NOTIFICATIONS:
   - Run queries #4, #5, #6, #7, or #8 to create test notifications
   - Refresh your browser page
   - Check if the notification badge appears

3. VERIFY BADGE COUNT:
   - Run query #2 to check unread count in database
   - Compare with badge count in browser
   - They should match

4. TEST MARK AS READ:
   - Note a specific notification ID from query #1
   - Use the mark as read button in browser
   - Run query #1 again to verify is_read changed to 1

5. CLEANUP:
   - Run query #12 to delete test notifications
   - Or run query #10 to mark all as read

EXAMPLE USAGE:
Replace [USER_ID] with actual ID (e.g., if user ID is 5):

INSERT INTO notifications (user_id, message, is_read, created_at) 
VALUES (5, 'Test notification', 0, NOW());

SELECT COUNT(*) as unread_count 
FROM notifications 
WHERE user_id = 5 AND is_read = 0;
*/
