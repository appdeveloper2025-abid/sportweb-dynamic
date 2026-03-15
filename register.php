<?php
session_start();
require_once 'config/database.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $phone = trim($_POST['phone']);
    $city = trim($_POST['city']);
    $role = 'Student';
    
    if (empty($name) || empty($email) || empty($password)) {
        $error = 'Please fill all required fields.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Invalid email format.';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters.';
    } elseif ($password !== $confirm_password) {
        $error = 'Passwords do not match.';
    } else {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        
        if ($stmt->fetch()) {
            $error = 'Email already registered.';
        } else {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (name, email, password, phone, city, role) VALUES (?, ?, ?, ?, ?, ?)");
            
            if ($stmt->execute([$name, $email, $hashedPassword, $phone, $city, $role])) {
                $success = 'Registration successful! You can now login.';
            } else {
                $error = 'Registration failed. Please try again.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - KPK Sports</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .password-toggle {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            font-size: 1.2rem;
            user-select: none;
        }
        .password-wrapper {
            position: relative;
        }
    </style>
</head>
<body>
    <nav class="navbar slide-in-down">
        <a href="index.php" class="logo">🏆 KPK Sports Management</a>
        <ul class="nav-links">
            <li><a href="login.php">Login</a></li>
            <li><a href="register.php">Register</a></li>
        </ul>
    </nav>

    <div class="container">
        <div style="max-width: 600px; margin: 2rem auto;">
            <div class="glass-card zoom-in">
                <h2 style="margin-bottom: 2rem; text-align: center; text-shadow: 2px 2px 4px rgba(0,0,0,0.3);">Create Account</h2>
                
                <?php if ($error): ?>
                    <div class="alert alert-danger shake"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>
                
                <?php if ($success): ?>
                    <div class="alert alert-success fade-in"><?= htmlspecialchars($success) ?></div>
                    <a href="login.php" class="btn btn-primary" style="width: 100%; text-align: center; display: block; margin-top: 1rem;">Go to Login</a>
                <?php else: ?>
                
                <form method="POST">
                    <div class="form-group slide-in-left" style="animation-delay: 0.1s;">
                        <label style="text-shadow: 1px 1px 2px rgba(0,0,0,0.3);">Full Name *</label>
                        <input type="text" name="name" class="form-control" required placeholder="Enter your full name" value="<?= htmlspecialchars($_POST['name'] ?? '') ?>">
                    </div>
                    
                    <div class="form-group slide-in-right" style="animation-delay: 0.2s;">
                        <label style="text-shadow: 1px 1px 2px rgba(0,0,0,0.3);">Email *</label>
                        <input type="email" name="email" class="form-control" required placeholder="your.email@example.com" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                    </div>
                    
                    <div class="form-group slide-in-left" style="animation-delay: 0.3s;">
                        <label style="text-shadow: 1px 1px 2px rgba(0,0,0,0.3);">Password * (min 6 characters)</label>
                        <div class="password-wrapper">
                            <input type="password" name="password" id="password" class="form-control" required placeholder="Enter password">
                            <span class="password-toggle" onclick="togglePassword('password', this)">👁️</span>
                        </div>
                    </div>
                    
                    <div class="form-group slide-in-right" style="animation-delay: 0.4s;">
                        <label style="text-shadow: 1px 1px 2px rgba(0,0,0,0.3);">Confirm Password *</label>
                        <div class="password-wrapper">
                            <input type="password" name="confirm_password" id="confirm_password" class="form-control" required placeholder="Confirm password">
                            <span class="password-toggle" onclick="togglePassword('confirm_password', this)">👁️</span>
                        </div>
                    </div>
                    
                    <div class="form-group slide-in-left" style="animation-delay: 0.5s;">
                        <label style="text-shadow: 1px 1px 2px rgba(0,0,0,0.3);">Phone</label>
                        <input type="text" name="phone" class="form-control" placeholder="03001234567" value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>">
                    </div>
                    
                    <div class="form-group slide-in-right" style="animation-delay: 0.6s;">
                        <label style="text-shadow: 1px 1px 2px rgba(0,0,0,0.3);">City</label>
                        <select name="city" class="form-control">
                            <option value="">Select City</option>
                            <option value="Peshawar">Peshawar</option>
                            <option value="Mardan">Mardan</option>
                            <option value="Abbottabad">Abbottabad</option>
                            <option value="Swat">Swat</option>
                            <option value="Kohat">Kohat</option>
                            <option value="Dera Ismail Khan">Dera Ismail Khan</option>
                            <option value="Bannu">Bannu</option>
                            <option value="Mansehra">Mansehra</option>
                            <option value="Charsadda">Charsadda</option>
                            <option value="Nowshera">Nowshera</option>
                        </select>
                    </div>
                    
                    <button type="submit" class="btn btn-primary slide-in-up" style="width: 100%; animation-delay: 0.7s;">Register</button>
                </form>
                
                <p style="text-align: center; margin-top: 1.5rem; text-shadow: 1px 1px 2px rgba(0,0,0,0.3);" class="fade-in" style="animation-delay: 0.8s;">
                    Already have an account? <a href="login.php" style="color: #fff; font-weight: bold;">Login here</a>
                </p>
                
                <?php endif; ?>
            </div>
        </div>
    </div>

    <footer class="slide-in-up" style="animation-delay: 0.9s;">
        <p style="text-shadow: 1px 1px 2px rgba(0,0,0,0.3);">Developed by ABID MEHMOOD | Phone: 03029382306</p>
    </footer>

    <script>
        function togglePassword(fieldId, icon) {
            const field = document.getElementById(fieldId);
            if (field.type === 'password') {
                field.type = 'text';
                icon.textContent = '🙈';
            } else {
                field.type = 'password';
                icon.textContent = '👁️';
            }
        }
    </script>
</body>
</html>
