<?php
session_start();
require_once 'config/database.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    
    if (empty($email) || empty($password)) {
        $error = 'Please enter email and password.';
    } else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_role'] = $user['role'];
            header('Location: dashboard.php');
            exit();
        } else {
            $error = 'Invalid email or password.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - KPK Sports</title>
    <link rel="stylesheet" href="assets/css/style.css">
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
        <div style="max-width: 500px; margin: 4rem auto;">
            <div class="glass-card zoom-in">
                <h2 style="margin-bottom: 2rem; text-align: center; text-shadow: 2px 2px 4px rgba(0,0,0,0.3);">Login</h2>
                
                <?php if ($error): ?>
                    <div class="alert alert-danger shake"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>
                
                <form method="POST">
                    <div class="form-group slide-in-left" style="animation-delay: 0.1s;">
                        <label style="text-shadow: 1px 1px 2px rgba(0,0,0,0.3);">Email</label>
                        <input type="email" name="email" class="form-control" required placeholder="admin@kpk.com">
                    </div>
                    
                    <div class="form-group slide-in-right" style="animation-delay: 0.2s;">
                        <label style="text-shadow: 1px 1px 2px rgba(0,0,0,0.3);">Password</label>
                        <input type="password" name="password" class="form-control" required placeholder="Enter password">
                    </div>
                    
                    <button type="submit" class="btn btn-primary slide-in-up" style="width: 100%; animation-delay: 0.3s;">Login</button>
                </form>
                
                <p style="text-align: center; margin-top: 1.5rem; text-shadow: 1px 1px 2px rgba(0,0,0,0.3);" class="fade-in" style="animation-delay: 0.4s;">
                    Don't have an account? <a href="register.php" style="color: #fff; font-weight: bold;">Register here</a>
                </p>
                
                <div style="margin-top: 2rem; padding-top: 1.5rem; border-top: 1px solid rgba(255, 255, 255, 0.3);" class="fade-in" style="animation-delay: 0.5s;">
                    <p style="text-align: center; opacity: 0.9; font-size: 0.875rem; text-shadow: 1px 1px 2px rgba(0,0,0,0.3);"><strong>Demo Login:</strong></p>
                    <p style="text-align: center; opacity: 0.9; font-size: 0.875rem; text-shadow: 1px 1px 2px rgba(0,0,0,0.3);">Email: admin@kpk.com</p>
                    <p style="text-align: center; opacity: 0.9; font-size: 0.875rem; text-shadow: 1px 1px 2px rgba(0,0,0,0.3);">Password: admin123</p>
                </div>
            </div>
        </div>
    </div>

    <footer class="slide-in-up" style="animation-delay: 0.6s;">
        <p style="text-shadow: 1px 1px 2px rgba(0,0,0,0.3);">Developed by ABID MEHMOOD | Phone: 03029382306</p>
    </footer>
</body>
</html>
