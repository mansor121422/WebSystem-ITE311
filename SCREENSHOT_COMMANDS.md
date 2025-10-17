# 📸 Screenshot Commands for Task 2 Evidence

## Required Screenshots for Task 2 (25 points)

Follow these commands in order to capture evidence for each requirement.

---

## 🔴 SCREENSHOT 1: Migration Schema & Execution (10 points)

### Command 1A: Show Migration Status
```bash
php spark migrate:status
```

**What to capture:**
- Shows `CreateAnnouncementsTable` migration
- Shows migration date/time (proves successful execution)

### Command 1B: Show Database Table Structure
```bash
php spark db:table announcements
```

**Alternative if above doesn't work:**
```bash
mysql -u root -e "USE lms_malik; DESCRIBE announcements;"
```

**What this shows:**
- Table exists (proves migration executed)
- Columns: id, title, content, created_at
- id is Primary Key with Auto Increment

### Command 1C: Show the Migration File
```bash
type app\Database\Migrations\2025-10-17-120000_CreateAnnouncementsTable.php
```

**What to capture:**
- Shows the complete migration schema
- Proves correct field definitions

---

## 🟢 SCREENSHOT 2: Properly Defined Model (5 points)

### Command 2: Display AnnouncementModel
```bash
type app\Models\AnnouncementModel.php
```

**What to capture:**
- Class extends CodeIgniter\Model
- $table = 'announcements'
- $primaryKey = 'id'
- $allowedFields defined
- $useTimestamps = true

---

## 🔵 SCREENSHOT 3: Controller Using Model Correctly (5 points)

### Command 3: Display Controller Code
```bash
type app\Controllers\Announcement.php
```

**What to capture:**
- Shows index() method
- Uses AnnouncementModel
- orderBy('created_at', 'DESC')
- findAll()

**Alternative - Show just the index() method:**
```bash
findstr /N /C:"public function index()" /C:"orderBy" /C:"created_at" /C:"DESC" app\Controllers\Announcement.php
```

---

## 🟡 SCREENSHOT 4: Seeder Creation & Execution (5 points)

### Command 4A: Show Seeder File
```bash
type app\Database\Seeds\AnnouncementSeeder.php
```

**What to capture:**
- At least 2 sample announcements
- Proper data structure

### Command 4B: Execute Seeder (with output)
```bash
php spark db:seed AnnouncementSeeder
```

**What to capture:**
- Success message
- Number of announcements seeded

### Command 4C: Verify Data in Database
```bash
mysql -u root -e "USE lms_malik; SELECT id, title, created_at FROM announcements ORDER BY created_at DESC;"
```

**What to capture:**
- At least 2 announcements exist
- Ordered by created_at descending
- Shows newest first

---

## 🎯 BONUS: Show Complete Working System

### Command 5: Show All Announcements with Full Details
```bash
mysql -u root -e "USE lms_malik; SELECT * FROM announcements ORDER BY created_at DESC;"
```

**What to capture:**
- All fields populated
- Correct ordering (newest first)
- Proves complete integration

---

## 📋 Complete Screenshot Sequence

Run these commands in order and take screenshots:

```powershell
# Screenshot 1: Migration Status
php spark migrate:status

# Screenshot 2: Table Structure
mysql -u root -e "USE lms_malik; DESCRIBE announcements;"

# Screenshot 3: Migration File Content
type app\Database\Migrations\2025-10-17-120000_CreateAnnouncementsTable.php

# Screenshot 4: Model File Content
type app\Models\AnnouncementModel.php

# Screenshot 5: Controller File Content
type app\Controllers\Announcement.php

# Screenshot 6: Seeder File Content
type app\Database\Seeds\AnnouncementSeeder.php

# Screenshot 7: Run Seeder (or show it was already run)
php spark db:seed AnnouncementSeeder

# Screenshot 8: Verify Database Data
mysql -u root -e "USE lms_malik; SELECT id, title, LEFT(content, 50) as content_preview, created_at FROM announcements ORDER BY created_at DESC;"
```

---

## 💡 Tips for Good Screenshots

1. **Clear terminal** before each command:
   ```bash
   cls
   ```

2. **Make text readable:**
   - Increase terminal font size
   - Use full screen
   - Dark background with light text works best

3. **Show the command AND output:**
   - Command at top
   - Full output below

4. **Annotate if needed:**
   - Use arrows or highlights
   - Add text boxes explaining key points

---

## 📊 What Each Screenshot Proves

| Screenshot | Proves | Points |
|------------|--------|--------|
| Migration Status | Migration exists & executed | 10 |
| Table Structure | Correct schema (id, title, content, created_at) | 10 |
| Model File | Properly configured | 5 |
| Controller File | Uses model with correct ordering | 5 |
| Seeder File | At least 2 sample announcements | 5 |
| Seeder Execution | Successfully inserted data | 5 |
| Database Query | Data exists and ordered correctly | All |

---

## 🎬 Ready to Start?

Open your terminal and run the commands above one by one!

