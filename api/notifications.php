<?php
require_once '../config/database.php';
require_once '../includes/security.php';
startSecureSession();

header('Content-Type: application/json');

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$userId = $_SESSION['user_id'];
$userRole = $_SESSION['user_role'];
$count = 0;

if ($userRole === 'Team Leader') {
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM team_members tm JOIN teams t ON tm.team_id = t.id WHERE t.leader_id = ? AND tm.status = 'Pending'");
    $stmt->execute([$userId]);
    $count = $stmt->fetch()['count'];
} elseif ($userRole === 'Player') {
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM team_members WHERE user_id = ? AND status = 'Pending'");
    $stmt->execute([$userId]);
    $count = $stmt->fetch()['count'];
}

echo json_encode([
    'success' => true,
    'count' => $count
]);
?>
