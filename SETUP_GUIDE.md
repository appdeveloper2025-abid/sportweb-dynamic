# 🏆 KPK SPORTS MANAGEMENT SYSTEM
## Complete Setup Guide

**Developed by ABID MEHMOOD | Phone: 03029382306**

---

## 📋 STEP-BY-STEP INSTALLATION

### **STEP 1: Install XAMPP**
1. Download XAMPP from: https://www.apachefriends.org/
2. Install it (default location: `C:\xampp\`)
3. Complete installation

### **STEP 2: Start XAMPP Services**
1. Open **XAMPP Control Panel**
2. Click **Start** next to **Apache** (should turn green)
3. Click **Start** next to **MySQL** (should turn green)
4. Both must show "Running" status

### **STEP 3: Create Database**
1. Open browser
2. Go to: `http://localhost/phpmyadmin`
3. Click **"New"** in left sidebar
4. Database name: `kpk_sports` (exactly this name!)
5. Click **"Create"**

### **STEP 4: Import Database**
1. Click on `kpk_sports` database (left sidebar)
2. Click **"Import"** tab at top
3. Click **"Choose File"**
4. Select: `C:\Users\Admin\Desktop\Static Sport web 2026\database\schema.sql`
5. Click **"Go"** at bottom
6. Wait for success message
7. You should see 5 tables: users, sports, teams, tournaments, matches

### **STEP 5: Verify Project Location**
Make sure your project is at:
```
C:\xampp\htdocs\Static Sport web 2026\
```

If not, copy the entire folder there.

### **STEP 6: Access the Website**
Open browser and go to:
```
http://localhost/Static Sport web 2026/
```

You should see the beautiful home page!

### **STEP 7: Login**
1. Click **"Login"** button
2. Enter credentials:
   - **Email:** `admin@kpk.com`
   - **Password:** `admin123`
3. Click **"Login"**
4. You'll see the dashboard with charts!

---

## ✅ VERIFICATION CHECKLIST

- [ ] XAMPP installed
- [ ] Apache running (green in XAMPP)
- [ ] MySQL running (green in XAMPP)
- [ ] Database `kpk_sports` created
- [ ] Schema imported (5 tables visible)
- [ ] Project in `C:\xampp\htdocs\Static Sport web 2026\`
- [ ] Home page loads: `http://localhost/Static Sport web 2026/`
- [ ] Can login with admin@kpk.com / admin123
- [ ] Dashboard shows with charts

---

## 🎯 QUICK ACCESS URLS

| Page | URL |
|------|-----|
| **Home** | `http://localhost/Static Sport web 2026/` |
| **Login** | `http://localhost/Static Sport web 2026/login.php` |
| **Dashboard** | `http://localhost/Static Sport web 2026/dashboard.php` |

---

## 🔑 LOGIN CREDENTIALS

### Admin Account:
- **Email:** admin@kpk.com
- **Password:** admin123

---

## 🎨 FEATURES INCLUDED

✅ Beautiful nature background on all pages
✅ Enhanced glassmorphism design
✅ Graphical charts (Chart.js)
✅ Student management system
✅ Team management
✅ Tournament system
✅ Match scheduling
✅ Real-time statistics
✅ Responsive design (mobile to 4K)

---

## 🐛 TROUBLESHOOTING

### Problem: "Database Error"
**Solution:**
1. Make sure MySQL is running (green in XAMPP)
2. Check database name is exactly: `kpk_sports`
3. Re-import schema.sql

### Problem: "Page not found"
**Solution:**
1. Check Apache is running (green in XAMPP)
2. Verify project location: `C:\xampp\htdocs\Static Sport web 2026\`
3. Use correct URL with space: `http://localhost/Static Sport web 2026/`

### Problem: "Can't login"
**Solution:**
1. Make sure you imported schema.sql
2. Check if admin user exists in phpMyAdmin
3. Use exact credentials: admin@kpk.com / admin123

### Problem: "Blank page"
**Solution:**
1. Check if all 5 tables exist in database
2. Re-import schema.sql
3. Restart Apache in XAMPP

---

## 📊 DATABASE TABLES

1. **users** - Admin, coaches, students
2. **sports** - Football, cricket, hockey, etc.
3. **teams** - School/college teams
4. **tournaments** - Competitions
5. **matches** - Match schedules and scores

---

## 🚀 WHAT YOU CAN DO

After logging in as admin:
- ✅ View dashboard with graphical charts
- ✅ Manage sports categories
- ✅ Create and manage teams
- ✅ Setup tournaments
- ✅ Schedule matches
- ✅ View statistics

---

## 📞 SUPPORT

If you face any issues:

**Developer:** ABID MEHMOOD  
**Phone:** 03029382306

---

## 🎉 SUCCESS!

If you can see the dashboard with charts, congratulations! 
Your KPK Sports Management System is working perfectly!

---

**Developed by ABID MEHMOOD | Phone: 03029382306**
