<?php
require_once '../config/database.php';
require_once '../includes/security.php';
startSecureSession();
requireRole('Player');

$userId = $_SESSION['user_id'];

// Get all tournaments with application status
$stmt = $pdo->prepare("SELECT t.*, s.name as sport_name,
    (SELECT status FROM applications WHERE tournament_id = t.id AND user_id = ?) as application_status
    FROM tournaments t 
    JOIN sports s ON t.sport_id = s.id 
    ORDER BY t.tournament_date DESC");
$stmt->execute([$userId]);
$tournaments = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tournaments - Player</title>
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
        <h1 class="fade-in" style="margin-bottom: 2rem;">Tournaments</h1>
        
        <?php if (empty($tournaments)): ?>
            <div class="glass-card fade-in">
                <p style="opacity: 0.7;">No tournaments available yet.</p>
            </div>
        <?php else: ?>
            <div class="grid grid-2">
                <?php foreach ($tournaments as $tournament): ?>
                    <?php $isOpen = strtotime($tournament['registration_deadline']) > time(); ?>
                    <div class="glass-card fade-in">
                        <h3 style="margin-bottom: 1rem;"><?= e($tournament['name']) ?></h3>
                        <p style="opacity: 0.8; margin-bottom: 0.5rem;"><strong>Sport:</strong> <?= e($tournament['sport_name']) ?></p>
                        <p style="opacity: 0.8; margin-bottom: 0.5rem;"><strong>Date:</strong> <?= date('M d, Y', strtotime($tournament['tournament_date'])) ?></p>
                        <p style="opacity: 0.8; margin-bottom: 0.5rem;"><strong>Time:</strong> <?= date('h:i A', strtotime($tournament['tournament_time'])) ?></p>
                        <p style="opacity: 0.8; margin-bottom: 0.5rem;"><strong>Location:</strong> <?= e($tournament['location']) ?></p>
                        <p style="opacity: 0.8; margin-bottom: 0.5rem;"><strong>Registration Deadline:</strong> <?= date('M d, Y H:i', strtotime($tournament['registration_deadline'])) ?></p>
                        <p style="opacity: 0.8; margin-bottom: 1rem;"><?= e($tournament['description']) ?></p>
                        
                        <?php if ($tournament['application_status'] === 'Approved'): ?>
                            <span class="badge badge-success">Application Approved</span>
                        <?php elseif ($tournament['application_status'] === 'Pending'): ?>
                            <span class="badge badge-warning">Application Pending</span>
                        <?php elseif ($tournament['application_status'] === 'Rejected'): ?>
                            <span class="badge badge-danger">Application Rejected</span>
                        <?php elseif ($isOpen): ?>
                            <a href="apply-tournament.php?id=<?= $tournament['id'] ?>" class="btn btn-sm btn-primary">Apply Now</a>
                        <?php else: ?>
                            <span class="badge badge-danger">Registration Closed</span>
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
