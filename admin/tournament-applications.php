<?php
require_once '../config/database.php';
require_once '../includes/security.php';
startSecureSession();
requireRole('Admin');

$tournament_id = (int)($_GET['id'] ?? 0);

if (!$tournament_id) {
    header('Location: tournaments.php');
    exit();
}

$stmt = $pdo->prepare("SELECT t.*, s.name as sport_name FROM tournaments t JOIN sports s ON t.sport_id = s.id WHERE t.id = ?");
$stmt->execute([$tournament_id]);
$tournament = $stmt->fetch();

if (!$tournament) {
    header('Location: tournaments.php');
    exit();
}

$stmt = $pdo->prepare("SELECT a.*, u.name as user_name, u.email, u.phone, tm.name as team_name FROM applications a JOIN users u ON a.user_id = u.id LEFT JOIN teams tm ON a.team_id = tm.id WHERE a.tournament_id = ? ORDER BY a.applied_at DESC");
$stmt->execute([$tournament_id]);
$applications = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tournament Applications - Admin</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <nav class="navbar">
        <a href="../dashboard.php" class="logo">⚽ Sports Management</a>
        <ul class="nav-links">
            <li><a href="../dashboard.php">Dashboard</a></li>
            <li><a href="tournaments.php">Tournaments</a></li>
            <li><a href="../logout.php">Logout</a></li>
        </ul>
    </nav>

    <div class="container">
        <div class="glass-card fade-in" style="margin-bottom: 2rem;">
            <h2><?= e($tournament['name']) ?></h2>
            <p><strong>Sport:</strong> <?= e($tournament['sport_name']) ?></p>
            <p><strong>Date:</strong> <?= date('M d, Y', strtotime($tournament['tournament_date'])) ?> at <?= date('h:i A', strtotime($tournament['tournament_time'])) ?></p>
            <p><strong>Location:</strong> <?= e($tournament['location']) ?></p>
            <p><strong>Registration Deadline:</strong> <?= date('M d, Y H:i', strtotime($tournament['registration_deadline'])) ?></p>
        </div>
        
        <div class="glass-card fade-in">
            <h3 style="margin-bottom: 1.5rem;">Applications (<?= count($applications) ?>)</h3>
            
            <?php if (empty($applications)): ?>
                <p style="opacity: 0.7;">No applications yet.</p>
            <?php else: ?>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>User</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Team</th>
                                <th>Status</th>
                                <th>Applied At</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($applications as $app): ?>
                                <tr>
                                    <td><?= e($app['id']) ?></td>
                                    <td><?= e($app['user_name']) ?></td>
                                    <td><?= e($app['email']) ?></td>
                                    <td><?= e($app['phone']) ?></td>
                                    <td><?= e($app['team_name'] ?? 'Individual') ?></td>
                                    <td>
                                        <?php if ($app['status'] === 'Approved'): ?>
                                            <span class="badge badge-success">Approved</span>
                                        <?php elseif ($app['status'] === 'Rejected'): ?>
                                            <span class="badge badge-danger">Rejected</span>
                                        <?php else: ?>
                                            <span class="badge badge-warning">Pending</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= date('M d, Y H:i', strtotime($app['applied_at'])) ?></td>
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
