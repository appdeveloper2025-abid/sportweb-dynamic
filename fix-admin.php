<?php
// Quick Fix: Insert Admin User with Correct Password
require_once 'config/database.php';

// Delete existing admin if exists
$pdo->exec("DELETE FROM users WHERE email = 'admin@sports.com'");

// Create new admin with correct password hash
$password = 'admin123';
$hash = password_hash($password, PASSWORD_DEFAULT);

$stmt = $pdo->prepare("INSERT INTO users (name, email, password, role, age, gender, skill_level) VALUES (?, ?, ?, ?, ?, ?, ?)");
$stmt->execute(['Admin', 'admin@sports.com', $hash, 'Admin', 30, 'Male', 'Professional']);

echo "✓ Admin user created successfully!\n\n";
echo "Login Credentials:\n";
echo "Email: admin@sports.com\n";
echo "Password: admin123\n\n";
echo "You can now delete this file (fix-admin.php) and login!\n";
?>
