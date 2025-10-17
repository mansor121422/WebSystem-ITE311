# Task 3: Testing Guide

## 🧪 Quick Test Scenarios (5 minutes)

Follow these steps to verify Task 3 implementation:

---

## ⚠️ Before Testing

**Make sure:**
1. ✅ MySQL is running in XAMPP
2. ✅ You have users with different roles:
   - At least one student
   - At least one teacher  
   - At least one admin

---

## Test 1: Student Login Redirection ✅

### Steps:
1. **Go to:** `http://localhost/ITE311-MALIK/login`
2. **Login** with student credentials
3. **Check URL** after login

### Expected Result:
- ✅ Redirected to: `/announcements`
- ✅ See announcements page
- ✅ Success message: "Welcome back, [Student Name]!"

### Screenshot: Student redirection to announcements

---

## Test 2: Teacher Login Redirection ✅

### Steps:
1. **Logout** (if logged in)
2. **Go to:** `http://localhost/ITE311-MALIK/login`
3. **Login** with teacher credentials
4. **Check URL** after login

### Expected Result:
- ✅ Redirected to: `/teacher/dashboard`
- ✅ See "Welcome, Teacher!" message
- ✅ Teacher name displayed
- ✅ Quick access links visible

### Screenshot: Teacher dashboard with welcome message

---

## Test 3: Admin Login Redirection ✅

### Steps:
1. **Logout** (if logged in)
2. **Go to:** `http://localhost/ITE311-MALIK/login`
3. **Login** with admin credentials
4. **Check URL** after login

### Expected Result:
- ✅ Redirected to: `/admin/dashboard`
- ✅ See "Welcome, Admin!" message
- ✅ Admin name displayed
- ✅ Quick access links visible (including Create Announcement)

### Screenshot: Admin dashboard with welcome message

---

## Test 4: Security - Unauthorized Access ✅

### Test 4A: Student Trying to Access Admin Dashboard

**Steps:**
1. **Login** as student
2. **Manually navigate** to: `http://localhost/ITE311-MALIK/admin/dashboard`

**Expected Result:**
- ✅ Redirected away from admin dashboard
- ✅ Error message: "Access denied. Administrators only."

### Test 4B: Student Trying to Access Teacher Dashboard

**Steps:**
1. **Login** as student
2. **Manually navigate** to: `http://localhost/ITE311-MALIK/teacher/dashboard`

**Expected Result:**
- ✅ Redirected away from teacher dashboard
- ✅ Error message: "Access denied. Teachers only."

### Screenshot: Security check working

---

## Test 5: Unauthenticated Access ✅

### Steps:
1. **Logout** (or open incognito window)
2. **Try to access:** `http://localhost/ITE311-MALIK/teacher/dashboard`

### Expected Result:
- ✅ Redirected to login page
- ✅ Error message: "You must be logged in to access the dashboard."

### Screenshot: Login redirect for unauthenticated user

---

## 📊 Quick Verification Table

| Test | What to Check | Pass/Fail |
|------|---------------|-----------|
| Student → /announcements | URL is /announcements | ⬜ |
| Teacher → /teacher/dashboard | URL is /teacher/dashboard | ⬜ |
| Admin → /admin/dashboard | URL is /admin/dashboard | ⬜ |
| Teacher dashboard shows "Welcome, Teacher!" | Message displays | ⬜ |
| Admin dashboard shows "Welcome, Admin!" | Message displays | ⬜ |
| Student can't access /admin/dashboard | Access denied | ⬜ |
| Student can't access /teacher/dashboard | Access denied | ⬜ |
| Unauthenticated can't access dashboards | Redirect to login | ⬜ |

---

## 🎬 Visual Verification

### What to Screenshot:

1. **Login page** with credentials entered
2. **Student redirect** to /announcements
3. **Teacher dashboard** showing:
   - "Welcome, Teacher!" heading
   - Teacher icon
   - User information
   - Quick access links
4. **Admin dashboard** showing:
   - "Welcome, Admin!" heading
   - Admin icon
   - User information
   - Quick access links
5. **Code screenshots:**
   - Auth::login() with role-based logic
   - Teacher::dashboard() method
   - Admin::dashboard() method
   - Routes configuration

---

## 🔍 Detailed Testing

### Test Teacher Dashboard Features

1. **Login** as teacher
2. **Verify elements:**
   - [ ] Header shows "Welcome, Teacher!"
   - [ ] Teacher icon (chalkboard-teacher) visible
   - [ ] Your name displayed correctly
   - [ ] Email displayed correctly
   - [ ] Role shows "Teacher"
   - [ ] "View Announcements" button works
   - [ ] "Full Dashboard" button works
   - [ ] Blue color scheme
   - [ ] Responsive on mobile

### Test Admin Dashboard Features

1. **Login** as admin
2. **Verify elements:**
   - [ ] Header shows "Welcome, Admin!"
   - [ ] Admin icon (user-shield) visible
   - [ ] Your name displayed correctly
   - [ ] Email displayed correctly
   - [ ] Role shows "Admin"
   - [ ] "View Announcements" button works
   - [ ] "Create Announcement" button works
   - [ ] "Full Dashboard" button works
   - [ ] Red color scheme
   - [ ] Responsive on mobile

---

## 🐛 Troubleshooting

### Issue 1: "Whoops!" error
**Solution:** Start MySQL in XAMPP

### Issue 2: Redirects to wrong page
**Solution:** 
1. Check user role in database
2. Clear browser cache
3. Logout and login again

### Issue 3: Can't access teacher/admin dashboard
**Solution:**
1. Verify you're logged in
2. Verify your role is correct
3. Check routes are configured

### Issue 4: No users to test with
**Solution:** Create test users:

```sql
-- Create teacher user
INSERT INTO users (name, email, password, role, created_at, updated_at) 
VALUES ('Teacher User', 'teacher@test.com', '$2y$10$...', 'teacher', NOW(), NOW());

-- Create admin user
INSERT INTO users (name, email, password, role, created_at, updated_at)
VALUES ('Admin User', 'admin@test.com', '$2y$10$...', 'admin', NOW(), NOW());
```

Or register through the registration page and update role in database.

---

## 📸 Screenshot Checklist

For your submission, capture:

1. [ ] Student login → Announcements page
2. [ ] Teacher login → Teacher dashboard
3. [ ] Admin login → Admin dashboard
4. [ ] Teacher dashboard - full view
5. [ ] Admin dashboard - full view
6. [ ] Auth::login() code with role-based redirection
7. [ ] Teacher.php controller code
8. [ ] Admin.php controller code
9. [ ] Routes.php with new routes
10. [ ] Security test (unauthorized access)

---

## ✅ Final Verification

**All tests passed if:**
- ✅ Students go to /announcements
- ✅ Teachers go to /teacher/dashboard
- ✅ Admins go to /admin/dashboard
- ✅ Dashboards show correct welcome messages
- ✅ Unauthorized access is blocked
- ✅ Unauthenticated users redirected to login

---

## 🎯 Time to Test!

**Estimated time:** 5 minutes for all tests

**Order:**
1. Test student login
2. Test teacher login
3. Test admin login
4. Test security (unauthorized access)
5. Take screenshots

---

**Ready? Start testing now!** 🚀

**Remember:** MySQL must be running in XAMPP!

