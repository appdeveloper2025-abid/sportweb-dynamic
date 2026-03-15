<?php
require_once '../config/database.php';
require_once '../includes/security.php';
startSecureSession();
requireRole('Team Leader');

$userId = $_SESSION['user_id'];
$team_id = (int)($_GET['id'] ?? 0);

// Verify team ownership
$stmt = $pdo->prepare("SELECT t.*, s.name as sport_name FROM teams t JOIN sports s ON t.sport_id = s.id WHERE t.id = ? AND t.leader_id = ?");
$stmt->execute([$team_id, $userId]);
$team = $stmt->fetch();

if (!$team) {
    header('Location: teams.php');
    exit();
}

$stmt = $pdo->prepare("SELECT tm.*, u.name, u.email, u.phone, u.skill_level FROM team_members tm JOIN users u ON tm.user_id = u.id WHERE tm.team_id = ? AND tm.status = 'Approved' ORDER BY tm.joined_at DESC");
$stmt->execute([$team_id]);
$members = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Team Members - Team Leader</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <nav class="navbar">
        <a href="../dashboard.php" class="logo">⚽ Sports Management</a>
        <ul class="nav-links">
            <li><a href="../dashboard.php">Dashboard</a></li>
            <li><a href="teams.php">My Teams</a></li>
            <li><a href="requests.php">Requests</a></li>
            <li><a href="../logout.php">Logout</a></li>
        </ul>
    </nav>

    <div class="container">
        <div class="glass-card fade-in" style="margin-bottom: 2rem;">
            <h2><?= e($team['name']) ?></h2>
            <p><strong>Sport:</strong> <?= e($team['sport_name']) ?></p>
            <p><strong>Description:</strong> <?= e($team['description']) ?></p>
            <p><strong>Max Members:</strong> <?= e($team['max_members']) ?></p>
        </div>
        
        <div class="glass-card fade-in">
            <h3 style="margin-bottom: 1.5rem;">Team Members (<?= count($members) ?>)</h3>
            
            <?php if (empty($members)): ?>
                <p style="opacity: 0.7;">No members yet.</p>
            <?php else: ?>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Skill Level</th>
                                <th>Joined At</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($members as $member): ?>
                                <tr>
                                    <td><?= e($member['name']) ?></td>
                                    <td><?= e($member['email']) ?></td>
                                    <td><?= e($member['phone']) ?></td>
                                    <td><?= e($member['skill_level']) ?></td>
                                    <td><?= date('M d, Y', strtotime($member['joined_at'])) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <footer>
        <p>Developed by ABID MEHMOOD | Phone: 03029382306</p>
    </footer>

    <script src="../assets/js/main.js"></script>
</body>
</html>
