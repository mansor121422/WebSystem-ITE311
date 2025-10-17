# Announcements Module Documentation

## Overview
The Announcements Module is a feature of the LMS System that allows administrators to post announcements visible to all logged-in users. This module enhances communication within the university's Online Student Portal.

---

## Features

### ✅ Completed Features
1. **View Announcements** - All logged-in users can view announcements
2. **Create Announcements** - Admin users can create new announcements
3. **Real-time Display** - Announcements are displayed with title, content, and date posted
4. **Responsive Design** - Mobile-friendly interface
5. **Role-Based Access** - Different permissions for Admin, Teacher, and Student roles
6. **Navigation Integration** - Announcements link added to all user navigation menus

---

## File Structure

```
app/
├── Controllers/
│   └── Announcement.php           # Announcement controller
├── Models/
│   └── AnnouncementModel.php      # Announcement model with database operations
├── Views/
│   ├── announcements.php          # Display announcements page
│   └── announcement_create.php    # Create announcement form (Admin only)
├── Database/
│   ├── Migrations/
│   │   └── 2025-10-17-120000_CreateAnnouncementsTable.php
│   └── Seeds/
│       └── AnnouncementSeeder.php # Sample data seeder
└── Config/
    └── Routes.php                 # Route configurations
```

---

## Database Schema

### Table: `announcements`

| Column        | Type         | Description                           |
|---------------|--------------|---------------------------------------|
| id            | INT(5)       | Primary key, auto-increment           |
| title         | VARCHAR(255) | Announcement title                    |
| content       | TEXT         | Announcement content                  |
| posted_by     | INT(5)       | Foreign key to users table            |
| date_posted   | DATETIME     | Date and time of posting              |
| created_at    | DATETIME     | Record creation timestamp             |
| updated_at    | DATETIME     | Record update timestamp               |

**Foreign Key Constraint:**
- `posted_by` references `users(id)` with CASCADE on DELETE and UPDATE

---

## Routes

| Method | Route                      | Controller Method         | Access Level        |
|--------|----------------------------|---------------------------|---------------------|
| GET    | `/announcements`           | `Announcement::index`     | All logged-in users |
| GET    | `/announcements/create`    | `Announcement::create`    | Admin only          |
| POST   | `/announcements/create`    | `Announcement::create`    | Admin only          |

---

## Controller Methods

### `Announcement::index()`
- **Purpose:** Display all announcements
- **Access:** All logged-in users (Student, Teacher, Admin)
- **Returns:** Announcements view with list of all announcements

### `Announcement::create()`
- **Purpose:** Create new announcement
- **Access:** Admin users only
- **Methods:** GET (display form) and POST (process submission)
- **Validation:** 
  - Title: Required
  - Content: Required
- **Returns:** Success/error message and redirects to announcements list

---

## Model Methods

### `AnnouncementModel` Methods

1. **`getAnnouncementsWithUser()`**
   - Fetches all announcements with author information
   - Joins with users table
   - Ordered by date posted (newest first)

2. **`getRecentAnnouncements($limit = 5)`**
   - Fetches recent announcements
   - Default limit: 5 announcements
   - Ordered by date posted (newest first)

3. **`getAnnouncementById($id)`**
   - Fetches single announcement with author information
   - Returns announcement details joined with user data

---

## User Interface

### Announcements List Page (`/announcements`)
- **Header:** Page title with "Create Announcement" button (Admin only)
- **Announcements Display:**
  - Title with icon
  - Posted date and time
  - Full content (with line breaks preserved)
  - Hover effect on cards
- **Empty State:** Message when no announcements exist

### Create Announcement Page (`/announcements/create`)
- **Form Fields:**
  - Title input (max 255 characters)
  - Content textarea (multiline)
- **Live Preview:** Real-time preview of announcement
- **Actions:** 
  - Publish button
  - Cancel button (returns to announcements list)

---

## Security Features

1. **Authentication Check:** All routes require user login
2. **Role-Based Access Control:** Only admins can create announcements
3. **CSRF Protection:** Form submissions include CSRF token
4. **Input Validation:** Title and content are validated before saving
5. **XSS Prevention:** All output is escaped using `esc()` function

---

## Installation & Setup

### Step 1: Run Migration
```bash
php spark migrate
```

This creates the `announcements` table in your database.

### Step 2: Seed Sample Data (Optional)
```bash
php spark db:seed AnnouncementSeeder
```

This populates the database with 4 sample announcements for testing.

### Step 3: Access the Feature
1. **Login** to the system (as any user role)
2. **Navigate** to "Announcements" in the navigation menu
3. **View** all announcements
4. **Create** (Admin only): Click "Create Announcement" button

---

## Usage Examples

### For Admin Users
1. Login as admin
2. Click "Announcements" in navigation
3. Click "Create Announcement" button
4. Fill in the form:
   - Title: "Important Update"
   - Content: "All students must complete their registration by Friday."
5. Click "Publish Announcement"
6. Announcement appears at the top of the list

### For Students and Teachers
1. Login to the system
2. Click "Announcements" in navigation
3. View all announcements sorted by date (newest first)
4. Read announcement details

---

## Testing Checklist

- [x] Migration creates announcements table
- [x] Seeder adds sample data successfully
- [x] Route `/announcements` is accessible to logged-in users
- [x] Unauthenticated users are redirected to login
- [x] Announcements display correctly (title, content, date)
- [x] Admin can access create announcement form
- [x] Non-admin users cannot access create form
- [x] Form validation works (empty fields rejected)
- [x] Announcements save to database
- [x] Navigation menu shows Announcements link for all roles
- [x] Responsive design works on mobile devices

---

## Future Enhancements (Optional)

Consider these additional features for future development:

1. **Edit/Delete Announcements:** Allow admins to modify or remove announcements
2. **Announcement Categories:** Group announcements by type (General, Academic, Technical)
3. **Read/Unread Status:** Track which announcements users have read
4. **Notifications:** Send email or push notifications for new announcements
5. **Attachments:** Allow file uploads with announcements
6. **Search Functionality:** Search announcements by title or content
7. **Pagination:** Display announcements in pages if there are many
8. **Draft Announcements:** Save announcements as drafts before publishing

---

## Troubleshooting

### Issue: "You must be logged in to view announcements"
**Solution:** Ensure you are logged in. Navigate to `/login` and authenticate.

### Issue: "Only administrators can create announcements"
**Solution:** Only users with role='admin' can create announcements. Check your user role in the database.

### Issue: No announcements displayed
**Solution:** 
1. Run the seeder: `php spark db:seed AnnouncementSeeder`
2. Or create announcements manually through the admin interface

### Issue: MySQL server has gone away
**Solution:** 
1. Ensure XAMPP MySQL service is running
2. Check database configuration in `app/Config/Database.php`

---

## Code Quality

✅ **No Linter Errors** - All files pass PHP linting
✅ **Following CodeIgniter 4 Conventions** - Proper MVC structure
✅ **Security Best Practices** - Authentication, CSRF, XSS prevention
✅ **Responsive Design** - Mobile-friendly interface
✅ **Clean Code** - Well-commented and organized

---

## Credits

**Developer:** AI Assistant  
**Framework:** CodeIgniter 4  
**Date:** October 17, 2025  
**Version:** 1.0.0

---

## Contact & Support

For questions or issues with the Announcements Module, please contact your system administrator.

