-- ===================================================
-- CLEAR OLD SEEDED ANNOUNCEMENTS
-- ===================================================
-- Run this in phpMyAdmin SQL tab if you want to start fresh
-- with only real-time announcements you create yourself

-- OPTION 1: Delete ALL announcements (fresh start)
TRUNCATE TABLE announcements;

-- OPTION 2: Delete only OLD seeded announcements (keep recent ones)
-- DELETE FROM announcements WHERE created_at < '2025-10-18 00:00:00';

-- OPTION 3: Delete specific announcements by ID
-- DELETE FROM announcements WHERE id IN (1, 2, 3, 4);

-- VERIFY: Check remaining announcements
SELECT id, title, created_at FROM announcements ORDER BY created_at DESC;

