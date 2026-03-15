<?php
require_once '../../config/database.php';
require_once '../../includes/security.php';
startSecureSession();
requireRole('Admin');

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $error = 'Invalid security token.';
    } else {
        $action = $_POST['action'] ?? '';
        
        if ($action === 'add_student') {
            // User data
            $name = sanitizeInput($_POST['name']);
            $email = sanitizeInput($_POST['email']);
            $password = $_POST['password'];
            $phone = sanitizeInput($_POST['phone']);
            $cnic = sanitizeInput($_POST['cnic']);
            $age = (int)$_POST['age'];
            $gender = sanitizeInput($_POST['gender']);
            $address = sanitizeInput($_POST['address']);
            $city = sanitizeInput($_POST['city']);
            
            // Student data
            $student_id = sanitizeInput($_POST['student_id']);
            $institution_name = sanitizeInput($_POST['institution_name']);
            $institution_type = sanitizeInput($_POST['institution_type']);
            $class_year = sanitizeInput($_POST['class_year']);
            $dob = sanitizeInput($_POST['date_of_birth']);
            $blood_group = sanitizeInput($_POST['blood_group']);
            $emergency_contact = sanitizeInput($_POST['emergency_contact']);
            $sport_interests = sanitizeInput($_POST['sport_interests']);
            $skill_level = sanitizeInput($_POST['skill_level']);
            
            if (empty($name) || empty($email) || empty($student_id) || empty($institution_name)) {
                $error = 'Please fill all required fields.';
            } elseif (!validateEmail($email)) {
                $error = 'Invalid email format.';
            } else {
                try {
                    $pdo->beginTransaction();
                    
                    // Insert user
                    $hashedPassword = hashPassword($password);
                    $stmt = $pdo->prepare("INSERT INTO users (name, email, password, phone, cnic, age, gender, address, city, role) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'Student')");
                    $stmt->execute([$name, $email, $hashedPassword, $phone, $cnic, $age, $gender, $address, $city]);
                    $user_id = $pdo->lastInsertId();
                    
                    // Insert student
                    $stmt = $pdo->prepare("INSERT INTO students (user_id, student_id, institution_name, institution_type, class_year, date_of_birth, blood_group, emergency_contact, sport_interests, skill_level) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                    $stmt->execute([$user_id, $student_id, $institution_name, $institution_type, $class_year, $dob, $blood_group, $emergency_contact, $sport_interests, $skill_level]);
                    
                    $pdo->commit();
                    $success = 'Student added successfully!';
                } catch (Exception $e) {
                    $pdo->rollBack();
                    $error = 'Failed to add student. Student ID or Email may already exist.';
                }
            }
        } elseif ($action === 'delete') {
            $user_id = (int)$_POST['user_id'];
            $stmt = $pdo->prepare("DELETE FROM users WHERE id = ? AND role = 'Student'");
            if ($stmt->execute([$user_id])) {
                $success = 'Student deleted successfully!';
            }
        }
    }
}

// Get all students with pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 20;
$offset = ($page - 1) * $per_page;

$search = $_GET['search'] ?? '';
$institution_filter = $_GET['institution'] ?? '';

$where = "WHERE u.role = 'Student'";
$params = [];

if ($search) {
    $where .= " AND (u.name LIKE ? OR u.email LIKE ? OR s.student_id LIKE ?)";
    $search_term = "%$search%";
    $params = [$search_term, $search_term, $search_term];
}

if ($institution_filter) {
    $where .= " AND s.institution_type = ?";
    $params[] = $institution_filter;
}

$stmt = $pdo->prepare("SELECT u.*, s.* FROM users u LEFT JOIN students s ON u.id = s.user_id $where ORDER BY u.created_at DESC LIMIT $per_page OFFSET $offset");
$stmt->execute($params);
$students = $stmt->fetchAll();

$stmt = $pdo->prepare("SELECT COUNT(*) as total FROM users u LEFT JOIN students s ON u.id = s.user_id $where");
$stmt->execute($params);
$total_students = $stmt->fetch()['total'];
$total_pages = ceil($total_students / $per_page);

// Get KPK cities
$stmt = $pdo->query("SELECT name FROM kpk_cities ORDER BY name");
$cities = $stmt->fetchAll();

$csrfToken = generateCSRFToken();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Management - KPK Sports</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>
    <nav class="navbar">
        <a href="../../dashboard.php" class="logo">🏆 KPK Sports Management</a>
        <ul class="nav-links">
            <li><a href="../../dashboard.php">Dashboard</a></li>
            <li><a href="manage.php">Students</a></li>
            <li><a href="../teams.php">Teams</a></li>
            <li><a href="../tournaments.php">Tournaments</a></li>
            <li><a href="../../logout.php">Logout</a></li>
        </ul>
    </nav>

    <div class="container">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
            <h1 class="fade-in" style="text-shadow: 2px 2px 4px rgba(0,0,0,0.5);">Student Management</h1>
            <button onclick="openModal('addStudentModal')" class="btn btn-primary">Add Student</button>
        </div>
        
        <?php if ($success): ?>
            <div class="alert alert-success fade-in"><?= e($success) ?></div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="alert alert-danger fade-in"><?= e($error) ?></div>
        <?php endif; ?>
        
        <!-- Search and Filter -->
        <div class="glass-card fade-in" style="margin-bottom: 2rem;">
            <form method="GET" style="display: flex; gap: 1rem; flex-wrap: wrap;">
                <input type="text" name="search" class="form-control" placeholder="Search by name, email, or student ID" value="<?= e($search) ?>" style="flex: 1; min-width: 250px;">
                <select name="institution" class="form-control" style="width: 200px;">
                    <option value="">All Institutions</option>
                    <option value="School" <?= $institution_filter === 'School' ? 'selected' : '' ?>>School</option>
                    <option value="College" <?= $institution_filter === 'College' ? 'selected' : '' ?>>College</option>
                    <option value="University" <?= $institution_filter === 'University' ? 'selected' : '' ?>>University</option>
                </select>
                <button type="submit" class="btn btn-primary">Search</button>
                <a href="manage.php" class="btn btn-success">Reset</a>
            </form>
        </div>
        
        <div class="glass-card fade-in">
            <h3 style="margin-bottom: 1.5rem; text-shadow: 1px 1px 2px rgba(0,0,0,0.3);">Total Students: <?= $total_students ?></h3>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Student ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Institution</th>
                            <th>Type</th>
                            <th>City</th>
                            <th>Skill Level</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($students as $student): ?>
                            <tr>
                                <td><?= e($student['student_id']) ?></td>
                                <td><?= e($student['name']) ?></td>
                                <td><?= e($student['email']) ?></td>
                                <td><?= e($student['institution_name']) ?></td>
                                <td><?= e($student['institution_type']) ?></td>
                                <td><?= e($student['city']) ?></td>
                                <td><?= e($student['skill_level']) ?></td>
                                <td>
                                    <a href="view.php?id=<?= $student['id'] ?>" class="btn btn-sm btn-primary">View</a>
                                    <form method="POST" style="display: inline;" onsubmit="return confirm('Delete this student?')">
                                        <input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="user_id" value="<?= $student['id'] ?>">
                                        <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <?php if ($total_pages > 1): ?>
                <div style="margin-top: 2rem; text-align: center;">
                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <a href="?page=<?= $i ?>&search=<?= urlencode($search) ?>&institution=<?= urlencode($institution_filter) ?>" 
                           class="btn btn-sm <?= $i === $page ? 'btn-primary' : 'btn-success' ?>" 
                           style="margin: 0 0.25rem;"><?= $i ?></a>
                    <?php endfor; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Add Student Modal -->
    <div id="addStudentModal" class="modal">
        <div class="modal-content" style="max-width: 800px;">
            <div class="modal-header">
                <h3>Add New Student</h3>
                <button class="modal-close" onclick="closeModal('addStudentModal')">&times;</button>
            </div>
            <form method="POST">
                <input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>">
                <input type="hidden" name="action" value="add_student">
                
                <h4 style="margin: 1.5rem 0 1rem; color: #6366f1;">Personal Information</h4>
                <div class="grid grid-2">
                    <div class="form-group">
                        <label>Full Name *</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Email *</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Password *</label>
                        <input type="password" name="password" class="form-control" required value="student123">
                    </div>
                    <div class="form-group">
                        <label>Phone</label>
                        <input type="text" name="phone" class="form-control" placeholder="03001234567">
                    </div>
                    <div class="form-group">
                        <label>CNIC</label>
                        <input type="text" name="cnic" class="form-control" placeholder="12345-1234567-1">
                    </div>
                    <div class="form-group">
                        <label>Date of Birth</label>
                        <input type="date" name="date_of_birth" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Age</label>
                        <input type="number" name="age" class="form-control" min="10" max="30">
                    </div>
                    <div class="form-group">
                        <label>Gender</label>
                        <select name="gender" class="form-control">
                            <option value="">Select</option>
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Blood Group</label>
                        <select name="blood_group" class="form-control">
                            <option value="">Select</option>
                            <option value="A+">A+</option>
                            <option value="A-">A-</option>
                            <option value="B+">B+</option>
                            <option value="B-">B-</option>
                            <option value="O+">O+</option>
                            <option value="O-">O-</option>
                            <option value="AB+">AB+</option>
                            <option value="AB-">AB-</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>City</label>
                        <select name="city" class="form-control">
                            <option value="">Select City</option>
                            <?php foreach ($cities as $city): ?>
                                <option value="<?= e($city['name']) ?>"><?= e($city['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                
                <div class="form-group">
                    <label>Address</label>
                    <textarea name="address" class="form-control" rows="2"></textarea>
                </div>
                
                <div class="form-group">
                    <label>Emergency Contact</label>
                    <input type="text" name="emergency_contact" class="form-control" placeholder="03001234567">
                </div>
                
                <h4 style="margin: 1.5rem 0 1rem; color: #6366f1;">Academic Information</h4>
                <div class="grid grid-2">
                    <div class="form-group">
                        <label>Student ID *</label>
                        <input type="text" name="student_id" class="form-control" required placeholder="STD-2024-001">
                    </div>
                    <div class="form-group">
                        <label>Institution Name *</label>
                        <input type="text" name="institution_name" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Institution Type *</label>
                        <select name="institution_type" class="form-control" required>
                            <option value="">Select</option>
                            <option value="School">School</option>
                            <option value="College">College</option>
                            <option value="University">University</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Class/Year</label>
                        <input type="text" name="class_year" class="form-control" placeholder="10th Grade / 1st Year">
                    </div>
                </div>
                
                <h4 style="margin: 1.5rem 0 1rem; color: #6366f1;">Sports Information</h4>
                <div class="grid grid-2">
                    <div class="form-group">
                        <label>Sport Interests</label>
                        <input type="text" name="sport_interests" class="form-control" placeholder="Football, Cricket">
                    </div>
                    <div class="form-group">
                        <label>Skill Level</label>
                        <select name="skill_level" class="form-control">
                            <option value="">Select</option>
                            <option value="Beginner">Beginner</option>
                            <option value="Intermediate">Intermediate</option>
                            <option value="Advanced">Advanced</option>
                            <option value="Professional">Professional</option>
                        </select>
                    </div>
                </div>
                
                <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 1rem;">Add Student</button>
            </form>
        </div>
    </div>

    <footer>
        <p style="text-shadow: 1px 1px 2px rgba(0,0,0,0.3);">Developed by ABID MEHMOOD | Phone: 03029382306</p>
    </footer>

    <script src="../../assets/js/main.js"></script>
</body>
</html>
