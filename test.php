<?php
// Test PHP and Database Connection
echo "<h1>Testing KPK Sports System</h1>";

// Test 1: PHP Version
echo "<h3>✓ PHP is working!</h3>";
echo "PHP Version: " . phpversion() . "<br><br>";

// Test 2: Database Connection
echo "<h3>Testing Database Connection...</h3>";
try {
    $pdo = new PDO(
        "mysql:host=localhost;dbname=sports_management;charset=utf8mb4",
        "root",
        "",
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    );
    echo "✓ Database connection successful!<br><br>";
    
    // Test 3: Check tables
    echo "<h3>Checking Database Tables...</h3>";
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    if (count($tables) > 0) {
        echo "✓ Found " . count($tables) . " tables:<br>";
        foreach ($tables as $table) {
            echo "- " . $table . "<br>";
        }
    } else {
        echo "✗ No tables found! Please import schema.sql<br>";
    }
    
    echo "<br><h3>✓ Everything is working!</h3>";
    echo "<a href='index.php' style='display:inline-block; padding:10px 20px; background:#6366f1; color:white; text-decoration:none; border-radius:5px; margin-top:20px;'>Go to Home Page</a>";
    
} catch(PDOException $e) {
    echo "✗ Database Error: " . $e->getMessage() . "<br><br>";
    echo "<strong>Solutions:</strong><br>";
    echo "1. Make sure MySQL is running in XAMPP<br>";
    echo "2. Create database 'sports_management' in phpMyAdmin<br>";
    echo "3. Import the schema.sql file<br>";
}
?>
