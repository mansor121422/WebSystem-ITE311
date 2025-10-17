# Task 4: Authorization Filter Testing Guide

## 🧪 Quick Security Test (5 minutes)

Test the RoleAuth filter to verify it properly restricts access based on user roles.

---

## ⚠️ Before Testing

**Make sure:**
1. ✅ MySQL is running in XAMPP
2. ✅ You have test accounts for each role:
   - Admin user
   - Teacher user
   - Student user
3. ✅ All files saved and no errors

---

## Test 1: Student Access Restrictions ✅

### Test 1A: Student Trying to Access Admin Dashboard

**Steps:**
1. **Login** as student
2. **Type in URL:** `http://localhost/ITE311-MALIK/admin/dashboard`
3. **Press Enter**

**Expected Result:**
- ✅ Redirected to `/announcements`
- ✅ Red error message appears: "Access Denied: Insufficient Permissions"
- ✅ Does NOT see admin dashboard

**Screenshot:** Student blocked from admin area

---

### Test 1B: Student Trying to Access Teacher Dashboard

**Steps:**
1. **Still logged in as student**
2. **Type in URL:** `http://localhost/ITE311-MALIK/teacher/dashboard`
3. **Press Enter**

**Expected Result:**
- ✅ Redirected to `/announcements`
- ✅ Error message: "Access Denied: Insufficient Permissions"
- ✅ Does NOT see teacher dashboard

**Screenshot:** Student blocked from teacher area

---

### Test 1C: Student Trying to Create Announcement

**Steps:**
1. **Still logged in as student**
2. **Type in URL:** `http://localhost/ITE311-MALIK/announcements/create`
3. **Press Enter**

**Expected Result:**
- ✅ Redirected to `/announcements`
- ✅ Error message: "Access Denied: Insufficient Permissions"
- ✅ Cannot create announcements

---

### Test 1D: Student CAN Access Announcements

**Steps:**
1. **Still logged in as student**
2. **Click** Announcements link OR
3. **Navigate to:** `/announcements`

**Expected Result:**
- ✅ Successfully loads announcements page
- ✅ NO error messages
- ✅ Can view announcements

---

## Test 2: Teacher Access Restrictions ✅

### Test 2A: Teacher Trying to Access Admin Dashboard

**Steps:**
1. **Logout** and login as teacher
2. **Type in URL:** `http://localhost/ITE311-MALIK/admin/dashboard`
3. **Press Enter**

**Expected Result:**
- ✅ Redirected to `/announcements`
- ✅ Error message: "Access Denied: Insufficient Permissions"
- ✅ Does NOT see admin dashboard

---

### Test 2B: Teacher CAN Access Teacher Dashboard

**Steps:**
1. **Still logged in as teacher**
2. **Navigate to:** `/teacher/dashboard` OR
3. **Login normally** (should auto-redirect)

**Expected Result:**
- ✅ Successfully loads teacher dashboard
- ✅ Sees "Welcome, Teacher!" message
- ✅ NO error messages

---

### Test 2C: Teacher Trying to Create Announcement

**Steps:**
1. **Still logged in as teacher**
2. **Type in URL:** `/announcements/create`

**Expected Result:**
- ✅ Redirected to `/announcements`
- ✅ Error message: "Access Denied: Insufficient Permissions"
- ✅ Cannot create (admin-only feature)

---

## Test 3: Admin Full Access ✅

### Test 3A: Admin CAN Access Admin Dashboard

**Steps:**
1. **Logout** and login as admin
2. **Navigate to:** `/admin/dashboard` OR
3. **Login normally** (should auto-redirect)

**Expected Result:**
- ✅ Successfully loads admin dashboard
- ✅ Sees "Welcome, Admin!" message
- ✅ NO error messages
- ✅ Full access granted

---

### Test 3B: Admin CAN Create Announcements

**Steps:**
1. **Still logged in as admin**
2. **Click** "Create Announcement" OR
3. **Navigate to:** `/announcements/create`

**Expected Result:**
- ✅ Successfully loads create form
- ✅ NO error messages
- ✅ Can create announcements

---

## Test 4: Unauthenticated Access ✅

### Test 4A: Not Logged In

**Steps:**
1. **Logout** completely
2. **Type in URL:** `/admin/dashboard`
3. **Press Enter**

**Expected Result:**
- ✅ Redirected to `/login`
- ✅ Error message: "You must be logged in to access this page."
- ✅ Cannot access without login

---

## 📊 Quick Test Matrix

| Test | User Role | URL | Should Work? |
|------|-----------|-----|--------------|
| 1 | Student | /admin/dashboard | ❌ Denied |
| 2 | Student | /teacher/dashboard | ❌ Denied |
| 3 | Student | /announcements | ✅ Allowed |
| 4 | Student | /announcements/create | ❌ Denied |
| 5 | Teacher | /admin/dashboard | ❌ Denied |
| 6 | Teacher | /teacher/dashboard | ✅ Allowed |
| 7 | Teacher | /announcements/create | ❌ Denied |
| 8 | Admin | /admin/dashboard | ✅ Allowed |
| 9 | Admin | /announcements/create | ✅ Allowed |
| 10 | Guest (not logged in) | /admin/dashboard | ❌ Denied |

---

## 🎯 Visual Verification

### What to Look For:

1. **Error Message Display:**
   ```
   ┌────────────────────────────────────────┐
   │ ⚠️ Access Denied: Insufficient        │
   │    Permissions                         │
   └────────────────────────────────────────┘
   ```

2. **URL Changes:**
   - Attempted: `/admin/dashboard`
   - Actual: `/announcements` (redirected)

3. **Flash Message:**
   - Red background
   - Error icon
   - Clear message

---

## 🐛 Troubleshooting

### Issue 1: No error message appears
**Solution:**
1. Check if flash messages are displayed in view
2. Clear browser cache
3. Try different browser

### Issue 2: Still can access restricted pages
**Solution:**
1. Clear PHP cache: Delete `writable/cache/*`
2. Restart Apache in XAMPP
3. Verify filter is registered in Filters.php
4. Verify routes have ['filter' => 'roleauth']

### Issue 3: Everyone gets access denied
**Solution:**
1. Check user role in database (SELECT * FROM users)
2. Verify role is exactly 'admin', 'teacher', or 'student' (lowercase)
3. Check session data: var_dump(session()->get('role'))

---

## 📸 Screenshot Checklist

Capture these for submission:

1. [ ] RoleAuth.php filter code
2. [ ] Filters.php showing registration
3. [ ] Routes.php showing route groups
4. [ ] Student blocked from /admin/dashboard (error message)
5. [ ] Teacher blocked from /admin/dashboard (error message)
6. [ ] Admin successfully accessing /admin/dashboard
7. [ ] Student blocked from /announcements/create
8. [ ] Unauthenticated user redirected to login

---

## 🎬 Quick Demo Script

**Total time: 3 minutes**

```
1. Login as student (30 sec)
2. Try /admin/dashboard → See error (15 sec)
3. Try /teacher/dashboard → See error (15 sec)
4. Try /announcements/create → See error (15 sec)
5. Go to /announcements → Works! (15 sec)

6. Logout, login as teacher (30 sec)
7. Try /admin/dashboard → See error (15 sec)
8. Go to /teacher/dashboard → Works! (15 sec)

9. Logout, login as admin (30 sec)
10. Go to /admin/dashboard → Works! (15 sec)
11. Go to /announcements/create → Works! (15 sec)
```

---

## ✅ Success Criteria

**All tests passed if:**
- ✅ Students cannot access /admin/* routes
- ✅ Students cannot access /teacher/* routes
- ✅ Students CAN access /announcements
- ✅ Teachers cannot access /admin/* routes
- ✅ Teachers CAN access /teacher/* routes
- ✅ Admins CAN access /admin/* routes
- ✅ Only admins can create announcements
- ✅ Error message displays correctly
- ✅ Unauthenticated users redirected to login

---

## 🎯 Final Verification

Run through all tests and mark:

- [ ] Test 1: Student restrictions ✓
- [ ] Test 2: Teacher restrictions ✓
- [ ] Test 3: Admin full access ✓
- [ ] Test 4: Unauthenticated blocked ✓
- [ ] Error messages display ✓
- [ ] Redirects work correctly ✓

---

**If all checked, Task 4 is working correctly!** 🎉

**Ready to test? Start with Test 1!** 🚀

