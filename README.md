# Sports Management System

A fully functional, secure, real-time dynamic sports management website with role-based access control.

**Developed by ABID MEHMOOD | Phone: 03029382306**

## 🎯 Features

### Security Features
- ✅ Password hashing using `password_hash()` and `password_verify()`
- ✅ Secure PHP sessions with regeneration on login
- ✅ Auto session timeout (30 minutes)
- ✅ CSRF protection tokens for all forms
- ✅ SQL Injection prevention using PDO prepared statements
- ✅ XSS attack prevention with input sanitization and output escaping
- ✅ Role-based access control (Admin, Team Leader, Player)
- ✅ Server-side deadline validation
- ✅ Secure logout with session destruction

### Core Features
- 👤 User registration and login system
- 🏆 Sports management (Admin)
- 👥 Team creation and management
- 📅 Tournament creation with registration deadlines
- 🔔 Real-time updates using AJAX polling
- 📊 Role-based dashboards
- 🎨 Premium glassmorphism design
- 📱 Fully responsive (mobile to 4K)

### User Roles
1. **Admin**: Manage sports, teams, tournaments, and users
2. **Team Leader**: Manage team members, approve/reject join requests
3. **Player**: Browse teams, join teams, apply for tournaments

## 📁 Project Structure

```
sports-management/
├── config/
│   └── database.php          # Database configuration
├── includes/
│   └── security.php          # Security functions
├── assets/
│   ├── css/
│   │   └── style.css         # Glassmorphism design
│   └── js/
│       └── main.js           # Real-time updates
├── admin/
│   ├── sports.php            # Manage sports
│   ├── teams.php             # Manage teams
│   ├── tournaments.php       # Manage tournaments
│   ├── users.php             # Manage users
│   └── tournament-applications.php
├── team-leader/
│   ├── teams.php             # My teams
│   ├── requests.php          # Join requests
│   └── team-members.php      # View members
├── player/
│   ├── teams.php             # Browse teams
│   ├── tournaments.php       # Browse tournaments
│   └── apply-tournament.php  # Apply for tournament
├── api/
│   ├── notifications.php     # Real-time notifications
│   └── stats.php             # Real-time stats
├── database/
│   └── schema.sql            # Database schema
├── index.php                 # Landing page
├── login.php                 # Login page
├── register.php              # Registration page
├── dashboard.php             # Role-based dashboard
├── profile.php               # User profile
├── logout.php                # Logout
├── .htaccess                 # Security configuration
└── README.md                 # This file
```

## 💻 Local Setup (XAMPP)

### Prerequisites
- XAMPP (Apache + MySQL + PHP)
- Web browser

### Installation Steps

1. **Download and Install XAMPP**
   - Download from: https://www.apachefriends.org/
   - Install XAMPP on your computer

2. **Start Services**
   - Open XAMPP Control Panel
   - Start **Apache** and **MySQL**

3. **Create Database**
   - Open browser and go to: `http://localhost/phpmyadmin`
   - Click "New" to create a database
   - Name it: `sports_management`
   - Click "Create"

4. **Import Database Schema**
   - Select the `sports_management` database
   - Click "Import" tab
   - Choose file: `database/schema.sql`
   - Click "Go"

5. **Copy Project Files**
   - Copy the entire project folder to: `C:\xampp\htdocs\`
   - Rename folder to: `sports-management`

6. **Configure Database**
   - Open `config/database.php`
   - Update credentials if needed (default works for XAMPP):
     ```php
     define('DB_HOST', 'localhost');
     define('DB_USER', 'root');
     define('DB_PASS', '');
     define('DB_NAME', 'sports_management');
     ```

7. **Access Website**
   - Open browser and go to: `http://localhost/sports-management`

8. **Default Admin Login**
   - Email: `admin@sports.com`
   - Password: `admin123`

## 🌍 Free Hosting Deployment

### Option 1: InfinityFree

1. **Create Account**
   - Go to: https://infinityfree.net/
   - Click "Sign Up"
   - Create free account

2. **Create Website**
   - Click "Create Account"
   - Choose subdomain or use custom domain
   - Wait for account activation (instant)

3. **Upload Files**
   - Go to Control Panel
   - Click "File Manager" or use FTP
   - Upload all files to `htdocs` folder
   - **DO NOT** upload the `database` folder

4. **Create MySQL Database**
   - In Control Panel, click "MySQL Databases"
   - Create new database
   - Note: Database name, username, and password

5. **Import Database**
   - Go to phpMyAdmin (link in control panel)
   - Select your database
   - Click "Import"
   - Upload `database/schema.sql`
   - Click "Go"

6. **Update Configuration**
   - Edit `config/database.php` using File Manager
   - Update with your database credentials:
     ```php
     define('DB_HOST', 'sql123.infinityfree.com'); // Your DB host
     define('DB_USER', 'epiz_12345678');           // Your DB user
     define('DB_PASS', 'your_password');           // Your DB password
     define('DB_NAME', 'epiz_12345678_sports');    // Your DB name
     ```

7. **Test Website**
   - Visit your website URL
   - Login with admin credentials

### Option 2: 000WebHost

1. **Create Account**
   - Go to: https://www.000webhost.com/
   - Click "Sign Up Free"
   - Create account

2. **Create Website**
   - Click "Create Website"
   - Choose subdomain
   - Set password

3. **Upload Files**
   - Go to "File Manager"
   - Navigate to `public_html`
   - Upload all project files
   - **DO NOT** upload the `database` folder

4. **Create Database**
   - Go to "Database" section
   - Click "New Database"
   - Create database and user
   - Note credentials

5. **Import Database**
   - Click "Manage" on your database
   - Go to phpMyAdmin
   - Import `database/schema.sql`

6. **Update Configuration**
   - Edit `config/database.php`
   - Update database credentials

7. **Access Website**
   - Visit your website URL
   - Login with admin credentials

## 🔐 Security Best Practices Implemented

1. **Authentication**
   - Passwords hashed with `password_hash()`
   - Session regeneration on login
   - Auto logout after 30 minutes of inactivity

2. **Database**
   - PDO with prepared statements
   - No direct SQL queries with user input
   - Foreign key constraints

3. **Input Validation**
   - Server-side validation for all inputs
   - Email and phone format validation
   - Deadline validation for tournaments

4. **Output Protection**
   - All outputs escaped with `htmlspecialchars()`
   - XSS prevention

5. **CSRF Protection**
   - Tokens generated for all forms
   - Token verification on submission
   - Token expiration (1 hour)

6. **Access Control**
   - Role-based access control
   - Unauthorized access prevention
   - Session-based authentication

7. **Server Security**
   - `.htaccess` protection
   - Hidden error messages
   - Secure file permissions

## 🎨 Design Features

- Premium glassmorphism UI
- Dark gradient background
- Smooth animations
- Responsive design (mobile to 4K)
- Modern color scheme
- Intuitive navigation

## 🔔 Real-Time Features

- Auto-refresh dashboard stats (every 5 seconds)
- Real-time notification updates
- AJAX-based form submissions
- Dynamic content updates

## 📊 Database Tables

1. **users** - User accounts and profiles
2. **sports** - Sports categories
3. **teams** - Team information
4. **team_members** - Team membership with approval status
5. **tournaments** - Tournament details with deadlines
6. **applications** - Tournament applications

## 🎯 User Workflows

### Admin Workflow
1. Login as admin
2. Create sports categories
3. Create teams and assign leaders
4. Create tournaments with deadlines
5. View applications
6. Manage users and roles

### Team Leader Workflow
1. Login as team leader
2. View assigned teams
3. Edit team information
4. Approve/reject join requests
5. View team members

### Player Workflow
1. Register account
2. Login as player
3. Browse available teams
4. Request to join teams
5. Browse tournaments
6. Apply for tournaments (before deadline)

## 🛠️ Technologies Used

- **Frontend**: HTML5, CSS3, JavaScript
- **Backend**: PHP 7.4+
- **Database**: MySQL 5.7+
- **Security**: PDO, password_hash, CSRF tokens
- **Real-time**: AJAX, Fetch API

## 📝 Default Credentials

**Admin Account:**
- Email: admin@sports.com
- Password: admin123

**Note:** Change admin password after first login!

## 🐛 Troubleshooting

### Database Connection Error
- Check database credentials in `config/database.php`
- Ensure MySQL service is running
- Verify database name exists

### Session Timeout
- Sessions expire after 30 minutes
- Login again to continue

### CSRF Token Error
- Clear browser cache
- Refresh the page
- Try again

### File Upload Issues
- Check folder permissions (755)
- Verify PHP upload settings

## 📞 Support

**Developer:** ABID MEHMOOD  
**Phone:** 03029382306

## 📄 License

This project is created for educational and commercial purposes.

---

**Developed by ABID MEHMOOD | Phone: 03029382306**
