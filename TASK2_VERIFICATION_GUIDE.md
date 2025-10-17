# Task 2: Verification Guide

## 📋 Quick Verification Checklist

Follow these steps to verify Task 2 completion:

---

## ⚠️ IMPORTANT: Before Testing

### Make sure MySQL is running in XAMPP!
```
1. Open XAMPP Control Panel
2. Click START next to MySQL
3. Wait for green "Running" status
```

**If MySQL is not running, you'll get a "Whoops!" error.**

---

## Step 1: Verify Migration ✅

### Check Migration Status
```bash
php spark migrate:status
```

**Expected Output:**
```
Filename                                      Migrated On
2025-10-17-120000_CreateAnnouncementsTable    2025-10-17 02:16:00  ✓
```

### Verify in phpMyAdmin
1. Open: `http://localhost/phpmyadmin`
2. Select database: `lms_malik`
3. Click on table: `announcements`

**Expected Columns:**
- ✅ id (Primary Key, Auto Increment)
- ✅ title (VARCHAR 255)
- ✅ content (TEXT)
- ✅ created_at (DATETIME)
- Plus: posted_by, date_posted, updated_at (bonus fields)

---

## Step 2: Verify Model ✅

**File Location:** `app/Models/AnnouncementModel.php`

**Check These:**
- ✅ Class extends `CodeIgniter\Model`
- ✅ `$table = 'announcements'`
- ✅ `$primaryKey = 'id'`
- ✅ `$allowedFields` includes: title, content, created_at
- ✅ `$useTimestamps = true`

---

## Step 3: Verify Controller ✅

**File Location:** `app/Controllers/Announcement.php`

**Check index() Method:**
```php
public function index()
{
    // ✅ Uses AnnouncementModel
    $announcements = $this->announcementModel
                          ->orderBy('created_at', 'DESC')  // ✅ Orders by created_at
                          ->findAll();                     // ✅ Fetches all
    
    return view('announcements', $data);  // ✅ Passes to view
}
```

**Task 2 Requirements:**
- ✅ Uses AnnouncementModel
- ✅ Fetches all announcements
- ✅ Orders by `created_at` DESC (newest first)

---

## Step 4: Verify Seeder ✅

### Check Seeder File
**Location:** `app/Database/Seeds/AnnouncementSeeder.php`

**Requirements:**
- ✅ At least 2 sample announcements (we have 4)
- ✅ Each has: title, content, created_at

### Run Seeder (if not already run)
```bash
php spark db:seed AnnouncementSeeder
```

**Expected Output:**
```
Successfully seeded 4 announcements.
```

### Verify in Database
```sql
-- Run in phpMyAdmin SQL tab
SELECT id, title, created_at 
FROM announcements 
ORDER BY created_at DESC;
```

**Expected Result:**
```
+----+--------------------------------------------+---------------------+
| id | title                                      | created_at          |
+----+--------------------------------------------+---------------------+
|  4 | Important: Assignment Deadline Extension   | 2025-10-17 02:16:39 |
|  3 | System Maintenance Scheduled               | 2025-10-16 02:16:39 |
|  2 | New Course Offerings for Fall 2025         | 2025-10-15 02:16:39 |
|  1 | Welcome to the LMS System!                 | 2025-10-14 02:16:39 |
+----+--------------------------------------------+---------------------+
4 rows in set
```

---

## Step 5: Test in Browser ✅

### Access Announcements Page

**URL:** `http://localhost/ITE311-MALIK/announcements`

**Before Testing:**
1. ✅ Make sure MySQL is running in XAMPP
2. ✅ Login to your system (any user role)

**Expected Result:**
```
╔══════════════════════════════════════════════╗
║       🔊 Announcements                       ║
╠══════════════════════════════════════════════╣
║                                              ║
║  📣 Important: Assignment Deadline Extension ║
║  📅 Posted on: October 17, 2025, 2:16 AM    ║
║  Due to technical difficulties...            ║
║                                              ║
║  📣 System Maintenance Scheduled             ║
║  📅 Posted on: October 16, 2025, 2:16 AM    ║
║  Please be advised that routine...           ║
║                                              ║
║  📣 New Course Offerings for Fall 2025       ║
║  📅 Posted on: October 15, 2025, 2:16 AM    ║
║  We are pleased to announce...               ║
║                                              ║
║  📣 Welcome to the LMS System!               ║
║  📅 Posted on: October 14, 2025, 2:16 AM    ║
║  We are excited to announce...               ║
║                                              ║
╚══════════════════════════════════════════════╝
```

**Verify:**
- ✅ All 4 announcements displayed
- ✅ Ordered by newest first (most recent at top)
- ✅ Shows title, content, and date
- ✅ No errors

---

## Step 6: Verify Ordering ✅

**Task 2 Requirement:** Announcements ordered by `created_at` DESC

**How to Verify:**
1. Look at the dates on the announcements page
2. Most recent date should be at the TOP
3. Oldest date should be at the BOTTOM

**Example:**
```
✅ CORRECT ORDER (newest first):
   Oct 17, 2025  ← Newest
   Oct 16, 2025
   Oct 15, 2025
   Oct 14, 2025  ← Oldest

❌ WRONG ORDER (would be oldest first):
   Oct 14, 2025  ← Oldest
   Oct 15, 2025
   Oct 16, 2025
   Oct 17, 2025  ← Newest
```

---

## Task 2 Requirements Checklist

| # | Requirement | Points | Status |
|---|------------|--------|--------|
| 1 | Migration file named `CreateAnnouncementsTable` | - | ✅ |
| 2 | Table has `id` (Primary Key, Auto Increment) | - | ✅ |
| 3 | Table has `title` (VARCHAR) | - | ✅ |
| 4 | Table has `content` (TEXT) | - | ✅ |
| 5 | Table has `created_at` (DATETIME) | - | ✅ |
| 6 | Migration executed successfully | 10 | ✅ |
| 7 | AnnouncementModel created | - | ✅ |
| 8 | Model configured for `announcements` table | - | ✅ |
| 9 | Model properly defined | 5 | ✅ |
| 10 | Controller uses AnnouncementModel | - | ✅ |
| 11 | Fetches all announcements | - | ✅ |
| 12 | Orders by `created_at` DESC | - | ✅ |
| 13 | Correct use of Model in Controller | 5 | ✅ |
| 14 | Seeder created | - | ✅ |
| 15 | At least 2 sample announcements | - | ✅ |
| 16 | Seeder executed successfully | 5 | ✅ |
| **TOTAL** | | **25** | **✅** |

---

## Common Issues & Solutions

### Issue 1: "Whoops!" error when accessing /announcements
**Cause:** MySQL is not running
**Solution:**
```
1. Open XAMPP Control Panel
2. Click START next to MySQL
3. Try again
```

### Issue 2: No announcements showing
**Cause:** Seeder not run
**Solution:**
```bash
php spark db:seed AnnouncementSeeder
```

### Issue 3: Wrong order (oldest first instead of newest)
**Cause:** Incorrect ORDER BY
**Solution:** Already fixed - using `created_at DESC`

### Issue 4: Table doesn't exist
**Cause:** Migration not run
**Solution:**
```bash
php spark migrate
```

---

## SQL Verification Queries

Run these in phpMyAdmin to verify Task 2:

### Query 1: Check Table Structure
```sql
DESCRIBE announcements;
```

**Expected:** Shows id, title, content, created_at columns

### Query 2: Check Data Exists
```sql
SELECT COUNT(*) as total FROM announcements;
```

**Expected:** `total = 4` (or at least 2)

### Query 3: Verify Ordering
```sql
SELECT 
    id, 
    title, 
    created_at,
    CASE 
        WHEN @prev_date IS NULL OR @prev_date >= created_at 
        THEN 'CORRECT' 
        ELSE 'WRONG' 
    END as order_check,
    @prev_date := created_at as prev
FROM 
    announcements,
    (SELECT @prev_date := NULL) init
ORDER BY 
    created_at DESC;
```

**Expected:** All rows show `order_check = 'CORRECT'`

---

## Evidence for Submission

### Screenshots to Take:

1. **Migration Status**
   ```bash
   php spark migrate:status
   ```
   Screenshot showing successful migration

2. **phpMyAdmin Table Structure**
   - Navigate to `announcements` table
   - Screenshot showing columns

3. **Database Data**
   - SQL query: `SELECT * FROM announcements ORDER BY created_at DESC`
   - Screenshot showing data ordered correctly

4. **Browser View**
   - Screenshot of: `http://localhost/ITE311-MALIK/announcements`
   - Showing announcements in correct order

5. **Model File**
   - Screenshot of `app/Models/AnnouncementModel.php`

6. **Controller Code**
   - Screenshot of `index()` method in `app/Controllers/Announcement.php`

---

## Final Verification

✅ **Task 2 is complete if ALL of these are true:**

- [ ] Migration file exists
- [ ] Migration executed successfully
- [ ] Table `announcements` exists in database
- [ ] Table has columns: id, title, content, created_at
- [ ] AnnouncementModel.php exists
- [ ] Model is properly configured
- [ ] Controller uses model in index() method
- [ ] Controller orders by created_at DESC
- [ ] Seeder file exists
- [ ] At least 2 announcements in database
- [ ] Announcements page accessible
- [ ] Announcements display in correct order (newest first)

---

**If all checkboxes are ✅, Task 2 is complete! Score: 25/25 points** 🎉

---

## Quick Test Command

Run this to test everything at once:

```bash
# Test migration
php spark migrate:status

# Test seeder (if not already run)
php spark db:seed AnnouncementSeeder

# Then visit
http://localhost/ITE311-MALIK/announcements
```

---

**Ready for submission!** ✅

