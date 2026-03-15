<?php
require_once 'config/database.php';
require_once 'includes/security.php';
startSecureSession();
requireLogin();

$userId = $_SESSION['user_id'];
$success = '';
$error = '';

// Get user data
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $error = 'Invalid security token.';
    } else {
        $name = sanitizeInput($_POST['name']);
        $phone = sanitizeInput($_POST['phone']);
        $age = (int)$_POST['age'];
        $gender = sanitizeInput($_POST['gender']);
        $sport_interests = sanitizeInput($_POST['sport_interests']);
        $skill_level = sanitizeInput($_POST['skill_level']);
        
        if (empty($name)) {
            $error = 'Name is required.';
        } elseif (!empty($phone) && !validatePhone($phone)) {
            $error = 'Invalid phone number.';
        } else {
            $stmt = $pdo->prepare("UPDATE users SET name = ?, phone = ?, age = ?, gender = ?, sport_interests = ?, skill_level = ? WHERE id = ?");
            if ($stmt->execute([$name, $phone, $age, $gender, $sport_interests, $skill_level, $userId])) {
                $_SESSION['user_name'] = $name;
                $success = 'Profile updated successfully!';
                $user = array_merge($user, $_POST);
            } else {
                $error = 'Update failed. Please try again.';
            }
        }
    }
}

$csrfToken = generateCSRFToken();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - Sports Management</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <nav class="navbar">
        <a href="dashboard.php" class="logo">⚽ Sports Management</a>
        <ul class="nav-links">
            <li><a href="dashboard.php">Dashboard</a></li>
            <li><a href="profile.php">Profile</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </nav>

    <div class="container">
        <div style="max-width: 700px; margin: 2rem auto;">
            <div class="glass-card fade-in">
                <h2 style="margin-bottom: 2rem;">My Profile</h2>
                
                <?php if ($success): ?>
                    <div class="alert alert-success"><?= e($success) ?></div>
                <?php endif; ?>
                
                <?php if ($error): ?>
                    <div class="alert alert-danger"><?= e($error) ?></div>
                <?php endif; ?>
                
                <form method="POST" data-validate>
                    <input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>">
                    
                    <div class="form-group">
                        <label>Full Name *</label>
                        <input type="text" name="name" class="form-control" required value="<?= e($user['name']) ?>">
                    </div>
                    
                    <div class="form-group">
                        <label>Email (Cannot be changed)</label>
                        <input type="email" class="form-control" value="<?= e($user['email']) ?>" disabled>
                    </div>
                    
                    <div class="form-group">
                        <label>Role</label>
                        <input type="text" class="form-control" value="<?= e($user['role']) ?>" disabled>
                    </div>
                    
                    <div class="form-group">
                        <label>Phone</label>
                        <input type="text" name="phone" class="form-control" value="<?= e($user['phone']) ?>">
                    </div>
                    
                    <div class="form-group">
                        <label>Age</label>
                        <input type="number" name="age" class="form-control" min="10" max="100" value="<?= e($user['age']) ?>">
                    </div>
                    
                    <div class="form-group">
                        <label>Gender</label>
                        <select name="gender" class="form-control">
                            <option value="">Select Gender</option>
                            <option value="Male" <?= $user['gender'] === 'Male' ? 'selected' : '' ?>>Male</option>
                            <option value="Female" <?= $user['gender'] === 'Female' ? 'selected' : '' ?>>Female</option>
                            <option value="Other" <?= $user['gender'] === 'Other' ? 'selected' : '' ?>>Other</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>Sport Interests</label>
                        <input type="text" name="sport_interests" class="form-control" value="<?= e($user['sport_interests']) ?>">
                    </div>
                    
                    <div class="form-group">
                        <label>Skill Level</label>
                        <select name="skill_level" class="form-control">
                            <option value="">Select Skill Level</option>
                            <option value="Beginner" <?= $user['skill_level'] === 'Beginner' ? 'selected' : '' ?>>Beginner</option>
                            <option value="Intermediate" <?= $user['skill_level'] === 'Intermediate' ? 'selected' : '' ?>>Intermediate</option>
                            <option value="Advanced" <?= $user['skill_level'] === 'Advanced' ? 'selected' : '' ?>>Advanced</option>
                            <option value="Professional" <?= $user['skill_level'] === 'Professional' ? 'selected' : '' ?>>Professional</option>
                        </select>
                    </div>
                    
                    <button type="submit" class="btn btn-primary" style="width: 100%;">Update Profile</button>
                </form>
            </div>
        </div>
    </div>

    <footer>
        <p>Developed by ABID MEHMOOD | Phone: 03029382306</p>
    </footer>

    <script src="assets/js/main.js"></script>
</body>
</html>
