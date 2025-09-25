# Sample User Credentials

## Overview
This document contains the login credentials for all sample users created by the UserSeeder.

## 🔐 **Admin Users**

### 1. System Administrator
- **Username**: `admin`
- **Email**: `admin@lms.com`
- **Password**: `admin123`
- **Role**: Admin
- **Full Name**: System Administrator

### 2. Super Admin
- **Username**: `superadmin`
- **Email**: `superadmin@lms.com`
- **Password**: `super123`
- **Role**: Admin
- **Full Name**: Super Admin

---

## 👨‍🏫 **Instructor Users**

### 3. John Doe
- **Username**: `instructor1`
- **Email**: `john.doe@lms.com`
- **Password**: `instructor123`
- **Role**: Instructor
- **Full Name**: John Doe

### 4. Jane Smith
- **Username**: `instructor2`
- **Email**: `jane.smith@lms.com`
- **Password**: `instructor123`
- **Role**: Instructor
- **Full Name**: Jane Smith

### 5. Mike Wilson
- **Username**: `instructor3`
- **Email**: `mike.wilson@lms.com`
- **Password**: `instructor123`
- **Role**: Instructor
- **Full Name**: Mike Wilson

---

## 👨‍🎓 **Student Users**

### 6. Alice Johnson
- **Username**: `student1`
- **Email**: `alice.johnson@student.com`
- **Password**: `student123`
- **Role**: Student
- **Full Name**: Alice Johnson

### 7. Bob Brown
- **Username**: `student2`
- **Email**: `bob.brown@student.com`
- **Password**: `student123`
- **Role**: Student
- **Full Name**: Bob Brown

### 8. Carol Davis
- **Username**: `student3`
- **Email**: `carol.davis@student.com`
- **Password**: `student123`
- **Role**: Student
- **Full Name**: Carol Davis

### 9. David Miller
- **Username**: `student4`
- **Email**: `david.miller@student.com`
- **Password**: `student123`
- **Role**: Student
- **Full Name**: David Miller

### 10. Emma Wilson
- **Username**: `student5`
- **Email**: `emma.wilson@student.com`
- **Password**: `student123`
- **Role**: Student
- **Full Name**: Emma Wilson

### 11. Frank Garcia
- **Username**: `student6`
- **Email**: `frank.garcia@student.com`
- **Password**: `student123`
- **Role**: Student
- **Full Name**: Frank Garcia

### 12. Grace Lee
- **Username**: `student7`
- **Email**: `grace.lee@student.com`
- **Password**: `student123`
- **Role**: Student
- **Full Name**: Grace Lee

### 13. Henry Taylor
- **Username**: `student8`
- **Email**: `henry.taylor@student.com`
- **Password**: `student123`
- **Role**: Student
- **Full Name**: Henry Taylor

---

## 📊 **User Summary**

| Role | Count | Password |
|------|-------|----------|
| **Admin** | 2 | `admin123` / `super123` |
| **Instructor** | 3 | `instructor123` |
| **Student** | 8 | `student123` |
| **Total** | **13** | |

---

## 🚀 **How to Use**

### Running the Seeder
```bash
php spark db:seed UserSeeder
```

### Testing Login
1. Use any of the credentials above to test your login system
2. All users have `active` status
3. Passwords are properly hashed using `password_hash()`

### Security Notes
- **Change passwords** in production environment
- **Use strong passwords** for admin accounts
- **Implement rate limiting** for login attempts
- **Add two-factor authentication** for admin users

---

## 🔄 **Updating Users**

To modify user data:
1. Edit the `UserSeeder.php` file
2. Run `php spark db:seed UserSeeder` again
3. Or create individual user update methods

## 📝 **Customization**

You can easily modify the seeder to:
- Add more users
- Change default passwords
- Modify user roles
- Add profile pictures
- Set different statuses
