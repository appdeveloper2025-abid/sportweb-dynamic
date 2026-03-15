<?php
// KPK Sports Management System - Database Configuration
// Developed by ABID MEHMOOD | Phone: 03029382306

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'kpk_sports');

define('SESSION_TIMEOUT', 1800);
define('CSRF_TOKEN_EXPIRE', 3600);

try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );
} catch(PDOException $e) {
    die("Database Error: " . $e->getMessage());
}
?>
