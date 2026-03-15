<?php
require_once '../config/database.php';
require_once '../includes/security.php';
startSecureSession();
requireRole('Player');

$userId = $_SESSION['user_id'];
$tournament_id = (int)($_GET['id'] ?? 0);

if (!$tournament_id) {
    header('Location: tournaments.php');
    exit();
}

// Get tournament details
$stmt = $pdo->prepare("SELECT t.*, s.name as sport_name FROM tournaments t JOIN sports s ON t.sport_id = s.id WHERE t.id = ?");
$stmt->execute([$tournament_id]);
$tournament = $stmt->fetch();

if (!$tournament) {
    header('Location: tournaments.php');
    exit();
}

// Server-side deadline validation
$isOpen = strtotime($tournament['registration_deadline']) > time();

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $error = 'Invalid security token.';
    } elseif (!$isOpen) {
        $error = 'Registration deadline has passed. Applications are no longer accepted.';
    } else {
        $team_id = !empty($_POST['team_id']) ? (int)$_POST['team_id'] : null;
        
        // Check if already applied
        $stmt = $pdo->prepare("SELECT id FROM applications WHERE tournament_id = ? AND user_id = ?");
        $stmt->execute([$tournament_id, $userId]);
        
        if ($stmt->fetch()) {
            $error = 'You have already applied for this tournament.';
        } else {
            $stmt = $pdo->prepare("INSERT INTO applications (tournament_id, user_id, team_id, status) VALUES (?, ?, ?, 'Pending')");
            if ($stmt->execute([$tournament_id, $userId, $team_id])) {
                $success = 'Application submitted successfully!';
            } else {
                $error = 'Failed to submit application.';
            }
        }
    }
}

// Get user's teams
$stmt = $pdo->prepare("SELECT t.id, t.name FROM teams t JOIN team_members tm ON t.id = tm.team_id WHERE tm.user_id = ? AND tm.status = 'Approved'");
$stmt->execute([$userId]);
$myTeams = $stmt->fetchAll();

$csrfToken = generateCSRFToken();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Apply for Tournament - Player</title>
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
        <div style="max-width: 700px; margin: 2rem auto;">
            <div class="glass-card fade-in">
                <h2 style="margin-bottom: 2rem;">Apply for Tournament</h2>
                
                <div style="background: rgba(255, 255, 255, 0.05); padding: 1.5rem; border-radius: 10px; margin-bottom: 2rem;">
                    <h3 style="margin-bottom: 1rem;"><?= e($tournament['name']) ?></h3>
                    <p style="opacity: 0.8; margin-bottom: 0.5rem;"><strong>Sport:</strong> <?= e($tournament['sport_name']) ?></p>
                    <p style="opacity: 0.8; margin-bottom: 0.5rem;"><strong>Date:</strong> <?= date('M d, Y', strtotime($tournament['tournament_date'])) ?></p>
                    <p style="opacity: 0.8; margin-bottom: 0.5rem;"><strong>Time:</strong> <?= date('h:i A', strtotime($tournament['tournament_time'])) ?></p>
                    <p style="opacity: 0.8; margin-bottom: 0.5rem;"><strong>Location:</strong> <?= e($tournament['location']) ?></p>
                    <p style="opacity: 0.8; margin-bottom: 0.5rem;"><strong>Registration Deadline:</strong> <?= date('M d, Y H:i', strtotime($tournament['registration_deadline'])) ?></p>
                    
                    <?php if ($isOpen): ?>
                        <span class="badge badge-success">Registration Open</span>
                    <?php else: ?>
                        <span class="badge badge-danger">Registration Closed</span>
                    <?php endif; ?>
                </div>
                
                <?php if ($success): ?>
                    <div class="alert alert-success"><?= e($success) ?></div>
                    <a href="tournaments.php" class="btn btn-primary" style="width: 100%;">Back to Tournaments</a>
                <?php elseif (!$isOpen): ?>
                    <div class="alert alert-danger">Registration deadline has passed. You cannot apply for this tournament.</div>
                    <a href="tournaments.php" class="btn btn-primary" style="width: 100%;">Back to Tournaments</a>
                <?php else: ?>
                    <?php if ($error): ?>
                        <div class="alert alert-danger"><?= e($error) ?></div>
                    <?php endif; ?>
                    
                    <form method="POST">
                        <input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>">
                        
                        <div class="form-group">
                            <label>Apply as (Optional)</label>
                            <select name="team_id" class="form-control">
                                <option value="">Individual</option>
                                <?php foreach ($myTeams as $team): ?>
                                    <option value="<?= $team['id'] ?>"><?= e($team['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <small style="opacity: 0.7;">Select a team if you want to participate as a team member</small>
                        </div>
                        
                        <button type="submit" class="btn btn-primary" style="width: 100%;">Submit Application</button>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <footer>
        <p>Developed by ABID MEHMOOD | Phone: 03029382306</p>
    </footer>

    <script src="../assets/js/main.js"></script>
</body>
</html>
