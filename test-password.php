<?php
// Generate correct password hash for admin123
$password = 'admin123';
$hash = password_hash($password, PASSWORD_DEFAULT);
echo "Password: admin123\n";
echo "Hash: " . $hash . "\n\n";

// Test verification
if (password_verify($password, $hash)) {
    echo "✓ Password verification works!\n";
} else {
    echo "✗ Password verification failed!\n";
}
?>
