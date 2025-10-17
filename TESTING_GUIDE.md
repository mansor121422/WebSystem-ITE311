# Announcements Module - Testing Guide

## 🧪 Complete Testing Instructions

Follow this guide to test the Announcements Module implementation.

---

## Prerequisites

✅ **Before Testing:**
1. Ensure XAMPP is running (Apache + MySQL)
2. Database migrations have been run: `php spark migrate`
3. Sample data has been seeded: `php spark db:seed AnnouncementSeeder`
4. You have test accounts with different roles:
   - Admin user
   - Student user
   - Teacher user

---

## Test Scenario 1: View Announcements (All Users)

### Steps:
1. **Login** to the system with any user account (student, teacher, or admin)
2. Look at the **navigation menu** - you should see "Announcements" link
3. **Click** on "Announcements"
4. You should see the announcements page with 4 sample announcements

### Expected Results:
- ✅ Page displays at URL: `http://localhost/ITE311-MALIK/announcements`
- ✅ Page title shows "Announcements"
- ✅ Four announcements are displayed in card format:
  1. "Important: Assignment Deadline Extension"
  2. "System Maintenance Scheduled"
  3. "New Course Offerings for Fall 2025"
  4. "Welcome to the LMS System!"
- ✅ Each announcement shows:
  - Title with megaphone icon
  - Posted date (formatted nicely)
  - Full content
- ✅ Cards have hover effect
- ✅ Responsive design works on mobile

### Screenshots to Verify:
```
┌─────────────────────────────────────────┐
│  🔊 Announcements       [+ Create]      │ ← Create button only for admin
├─────────────────────────────────────────┤
│ ┌───────────────────────────────────┐   │
│ │ 📣 Important: Assignment Deadline │   │
│ │ 📅 Posted on: Oct 17, 2025...    │   │
│ │                                   │   │
│ │ Due to technical difficulties...  │   │
│ └───────────────────────────────────┘   │
│                                         │
│ ┌───────────────────────────────────┐   │
│ │ 📣 System Maintenance Scheduled   │   │
│ │ ...                               │   │
│ └───────────────────────────────────┘   │
└─────────────────────────────────────────┘
```

---

## Test Scenario 2: Create Announcement Button (Role-Based)

### Test 2A: As Student or Teacher
1. **Login** as a student or teacher
2. **Navigate** to Announcements page
3. **Look** for "Create Announcement" button

**Expected Result:**
- ❌ "Create Announcement" button should NOT be visible
- ✅ Only viewing capability is available

### Test 2B: As Admin
1. **Login** as an admin user
2. **Navigate** to Announcements page
3. **Look** for "Create Announcement" button

**Expected Result:**
- ✅ "Create Announcement" button IS visible at top-right
- ✅ Button has blue background with "+" icon

---

## Test Scenario 3: Create New Announcement (Admin Only)

### Steps:
1. **Login** as admin
2. **Click** "Announcements" in navigation
3. **Click** "Create Announcement" button
4. You should see the create form

### Expected Results:
- ✅ Page URL: `http://localhost/ITE311-MALIK/announcements/create`
- ✅ Page shows form with:
  - Title input field (max 255 characters)
  - Content textarea (multiline)
  - Publish button
  - Cancel button
- ✅ Preview section at bottom showing live updates

### Form Layout:
```
┌─────────────────────────────────────────┐
│  ➕ Create New Announcement             │
│  Share important information with all   │
├─────────────────────────────────────────┤
│                                         │
│  📝 Title *                             │
│  [_________________________________]    │
│                                         │
│  📄 Content *                           │
│  [_________________________________]    │
│  [                                   ]  │
│  [                                   ]  │
│                                         │
│  [📤 Publish] [❌ Cancel]              │
│                                         │
│ ┌─────────────────────────────────┐   │
│ │ 👁️ Preview                      │   │
│ │ Title will appear here          │   │
│ │ Content will appear here        │   │
│ └─────────────────────────────────┘   │
└─────────────────────────────────────────┘
```

---

## Test Scenario 4: Live Preview Functionality

### Steps:
1. Be on the create announcement page (as admin)
2. **Type** in the Title field: "Test Announcement"
3. **Type** in the Content field: "This is a test"

### Expected Results:
- ✅ As you type, the preview section updates in real-time
- ✅ Preview shows the exact text you're typing
- ✅ Formatting is preserved

---

## Test Scenario 5: Form Validation

### Test 5A: Empty Form Submission
1. On create page, leave both fields empty
2. **Click** "Publish Announcement"

**Expected Result:**
- ❌ Form should not submit
- ✅ Browser validation shows "Please fill out this field"

### Test 5B: Title Only
1. Enter title only
2. Leave content empty
3. **Click** "Publish Announcement"

**Expected Result:**
- ❌ Form should not submit
- ✅ Browser validation shows content is required

### Test 5C: Complete Form
1. **Enter** title: "Test Announcement"
2. **Enter** content: "This is a test message for all students."
3. **Click** "Publish Announcement"

**Expected Result:**
- ✅ Success message appears: "Announcement created successfully!"
- ✅ Redirected to announcements list
- ✅ New announcement appears at the TOP of the list
- ✅ Shows current date and time

---

## Test Scenario 6: Access Control (Security)

### Test 6A: Unauthenticated User
1. **Logout** from the system
2. Try to access: `http://localhost/ITE311-MALIK/announcements`

**Expected Result:**
- ✅ Redirected to login page
- ✅ Error message: "You must be logged in to view announcements."

### Test 6B: Non-Admin Trying to Create
1. **Login** as student
2. Try to access: `http://localhost/ITE311-MALIK/announcements/create`

**Expected Result:**
- ✅ Redirected to announcements list
- ✅ Error message: "Only administrators can create announcements."

---

## Test Scenario 7: Navigation Integration

### Test 7A: Student Navigation
1. **Login** as student
2. **Look** at navigation menu

**Expected Result:**
```
Dashboard | Courses | Announcements | Assignments | Grades | Logout
                         ↑ Should be here
```

### Test 7B: Admin Navigation
1. **Login** as admin
2. **Look** at navigation menu

**Expected Result:**
```
Dashboard | Announcements | Users | Courses | Reports | Settings | Logout
               ↑ Should be here
```

### Test 7C: Teacher Navigation
1. **Login** as teacher
2. **Look** at navigation menu

**Expected Result:**
```
Dashboard | Courses | Announcements | Students | Create | Lessons | Logout
                         ↑ Should be here
```

---

## Test Scenario 8: Responsive Design

### Steps:
1. Open announcements page
2. **Resize** browser window to mobile size (< 768px)

### Expected Results:
- ✅ Cards stack vertically
- ✅ Navigation becomes vertical
- ✅ Create button moves below title
- ✅ All content remains readable
- ✅ No horizontal scrolling

---

## Test Scenario 9: Database Verification

### Manual Database Check:
1. Open phpMyAdmin
2. Select your database
3. Open `announcements` table

**Expected Result:**
- ✅ Table exists with correct structure
- ✅ Contains sample announcements
- ✅ All fields have data (id, title, content, posted_by, date_posted, created_at, updated_at)
- ✅ Foreign key to users table works

### SQL Query to Test:
```sql
SELECT a.*, u.name as author_name 
FROM announcements a 
JOIN users u ON a.posted_by = u.id 
ORDER BY a.date_posted DESC;
```

**Expected Output:**
- Returns all announcements with author names
- Ordered by most recent first

---

## Test Scenario 10: Content Display

### Test Line Breaks:
1. Create announcement with multi-line content:
```
Line 1

Line 2

Line 3
```

**Expected Result:**
- ✅ Line breaks are preserved in display
- ✅ Content shows with proper spacing

### Test Special Characters:
1. Create announcement with: `<script>alert('test')</script>`

**Expected Result:**
- ✅ Special characters are escaped
- ✅ Script does NOT execute (XSS protection)
- ✅ Displays as plain text

---

## Common Issues & Solutions

### Issue 1: "Page Not Found" when accessing /announcements
**Solution:**
- Check Routes.php file has announcement routes
- Clear CodeIgniter cache: Delete files in `writable/cache/`

### Issue 2: No announcements showing
**Solution:**
- Run seeder: `php spark db:seed AnnouncementSeeder`
- Check database connection in `app/Config/Database.php`

### Issue 3: "Create Announcement" button visible to students
**Solution:**
- Check user role in database (should be 'admin' for admin users)
- Verify session data with: `var_dump(session()->get('role'));`

### Issue 4: Form doesn't submit
**Solution:**
- Check CSRF is enabled in `app/Config/Security.php`
- Ensure csrf_token() and csrf_hash() are in form

---

## Performance Testing

### Load Test:
1. Insert 100 announcements into database
2. Access announcements page
3. Verify it loads quickly (< 2 seconds)

### Sample SQL for bulk insert:
```sql
INSERT INTO announcements (title, content, posted_by, date_posted, created_at, updated_at)
SELECT 
    CONCAT('Announcement ', n) as title,
    CONCAT('Content for announcement ', n) as content,
    1 as posted_by,
    NOW() as date_posted,
    NOW() as created_at,
    NOW() as updated_at
FROM (
    SELECT @row := @row + 1 as n
    FROM (SELECT 0 UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3) t1,
         (SELECT 0 UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3) t2,
         (SELECT 0 UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3) t3,
         (SELECT @row:=4) t4
    LIMIT 100
) numbers;
```

---

## Final Checklist

Before marking Task 1 as complete, verify:

- [ ] Announcements page accessible at `/announcements`
- [ ] All logged-in users can view announcements
- [ ] Admin users can create announcements
- [ ] Non-admin users cannot access create page
- [ ] Form validation works correctly
- [ ] Announcements display with title, content, date
- [ ] Navigation links appear for all roles
- [ ] Responsive design works
- [ ] No linter errors
- [ ] Database migration successful
- [ ] Sample data seeded
- [ ] Security measures in place (auth, CSRF, XSS prevention)

---

## Test Results Template

Use this template to document your testing:

```
=== ANNOUNCEMENT MODULE TEST RESULTS ===
Date: __________________
Tester: __________________

Scenario 1 - View Announcements: [PASS/FAIL]
Scenario 2 - Create Button Visibility: [PASS/FAIL]
Scenario 3 - Create New Announcement: [PASS/FAIL]
Scenario 4 - Live Preview: [PASS/FAIL]
Scenario 5 - Form Validation: [PASS/FAIL]
Scenario 6 - Access Control: [PASS/FAIL]
Scenario 7 - Navigation Integration: [PASS/FAIL]
Scenario 8 - Responsive Design: [PASS/FAIL]
Scenario 9 - Database Verification: [PASS/FAIL]
Scenario 10 - Content Display: [PASS/FAIL]

Overall Status: [PASS/FAIL]
Notes: ________________________________
```

---

## Support

If you encounter any issues during testing:
1. Check the error logs in `writable/logs/`
2. Review the ANNOUNCEMENTS_MODULE_README.md
3. Verify database connection settings
4. Ensure XAMPP services are running

---

**Happy Testing! 🎉**

