<?php
require_once '../config/database.php';
require_once '../includes/security.php';
startSecureSession();
requireRole('Player');

$userId = $_SESSION['user_id'];
$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $error = 'Invalid security token.';
    } else {
        $team_id = (int)$_POST['team_id'];
        
        // Check if already requested
        $stmt = $pdo->prepare("SELECT id FROM team_members WHERE team_id = ? AND user_id = ?");
        $stmt->execute([$team_id, $userId]);
        
        if ($stmt->fetch()) {
            $error = 'You have already requested to join this team.';
        } else {
            $stmt = $pdo->prepare("INSERT INTO team_members (team_id, user_id, status) VALUES (?, ?, 'Pending')");
            if ($stmt->execute([$team_id, $userId])) {
                $success = 'Join request sent successfully!';
            } else {
                $error = 'Failed to send request.';
            }
        }
    }
}

// Get all teams with join status
$stmt = $pdo->prepare("SELECT t.*, s.name as sport_name, u.name as leader_name, 
    (SELECT COUNT(*) FROM team_members WHERE team_id = t.id AND status = 'Approved') as member_count,
    (SELECT status FROM team_members WHERE team_id = t.id AND user_id = ?) as join_status
    FROM teams t 
    JOIN sports s ON t.sport_id = s.id 
    JOIN users u ON t.leader_id = u.id 
    ORDER BY t.created_at DESC");
$stmt->execute([$userId]);
$teams = $stmt->fetchAll();

$csrfToken = generateCSRFToken();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Browse Teams - Player</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <nav class="navbar">
        <a href="../dashboard.php" class="logo">⚽ Sports Management</a>
        <ul class="nav-links">
            <li><a href="../dashboard.php">Dashboard</a></li>
            <li><a href="teams.php">Teams</a></li>
            <li><a href="tournaments.php">Tournaments</a></li>
            <li><a href="../profile.php">Profile</a></li>
            <li><a href="../logout.php">Logout</a></li>
        </ul>
    </nav>

    <div class="container">
        <h1 class="fade-in" style="margin-bottom: 2rem;">Browse Teams</h1>
        
        <?php if ($success): ?>
            <div class="alert alert-success fade-in"><?= e($success) ?></div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="alert alert-danger fade-in"><?= e($error) ?></div>
        <?php endif; ?>
        
        <?php if (empty($teams)): ?>
            <div class="glass-card fade-in">
                <p style="opacity: 0.7;">No teams available yet.</p>
            </div>
        <?php else: ?>
            <div class="grid grid-3">
                <?php foreach ($teams as $team): ?>
                    <div class="glass-card fade-in">
                        <h3 style="margin-bottom: 1rem;"><?= e($team['name']) ?></h3>
                        <p style="opacity: 0.8; margin-bottom: 0.5rem;"><strong>Sport:</strong> <?= e($team['sport_name']) ?></p>
                        <p style="opacity: 0.8; margin-bottom: 0.5rem;"><strong>Leader:</strong> <?= e($team['leader_name']) ?></p>
                        <p style="opacity: 0.8; margin-bottom: 0.5rem;"><strong>Members:</strong> <?= e($team['member_count']) ?> / <?= e($team['max_members']) ?></p>
                        <p style="opacity: 0.8; margin-bottom: 1rem;"><?= e($team['description']) ?></p>
                        
                        <?php if ($team['join_status'] === 'Approved'): ?>
                            <span class="badge badge-success">Member</span>
                        <?php elseif ($team['join_status'] === 'Pending'): ?>
                            <span class="badge badge-warning">Request Pending</span>
                        <?php elseif ($team['join_status'] === 'Rejected'): ?>
                            <span class="badge badge-danger">Request Rejected</span>
                        <?php elseif ($team['member_count'] < $team['max_members']): ?>
                            <form method="POST">
                                <input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>">
                                <input type="hidden" name="team_id" value="<?= $team['id'] ?>">
                                <button type="submit" class="btn btn-sm btn-primary">Request to Join</button>
                            </form>
                        <?php else: ?>
                            <span class="badge badge-danger">Team Full</span>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <footer>
        <p>Developed by ABID MEHMOOD | Phone: 03029382306</p>
    </footer>

    <script src="../assets/js/main.js"></script>
</body>
</html>
