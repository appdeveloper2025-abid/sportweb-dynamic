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
$stats = [];

if ($userRole === 'Admin') {
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM users");
    $stats['users'] = $stmt->fetch()['count'];
    
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM sports");
    $stats['sports'] = $stmt->fetch()['count'];
    
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM teams");
    $stats['teams'] = $stmt->fetch()['count'];
    
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM tournaments");
    $stats['tournaments'] = $stmt->fetch()['count'];
} elseif ($userRole === 'Team Leader') {
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM teams WHERE leader_id = ?");
    $stmt->execute([$userId]);
    $stats['my_teams'] = $stmt->fetch()['count'];
    
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM team_members tm JOIN teams t ON tm.team_id = t.id WHERE t.leader_id = ? AND tm.status = 'Pending'");
    $stmt->execute([$userId]);
    $stats['pending_requests'] = $stmt->fetch()['count'];
} else {
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM team_members WHERE user_id = ? AND status = 'Approved'");
    $stmt->execute([$userId]);
    $stats['my_teams'] = $stmt->fetch()['count'];
    
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM applications WHERE user_id = ?");
    $stmt->execute([$userId]);
    $stats['my_applications'] = $stmt->fetch()['count'];
}

echo json_encode([
    'success' => true,
    'stats' => $stats
]);
?>
