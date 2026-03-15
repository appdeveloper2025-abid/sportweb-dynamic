<?php
require_once '../config/database.php';
require_once '../includes/security.php';
startSecureSession();
requireRole('Team Leader');

$userId = $_SESSION['user_id'];
$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $error = 'Invalid security token.';
    } else {
        $action = $_POST['action'] ?? '';
        
        if ($action === 'update') {
            $team_id = (int)$_POST['team_id'];
            $name = sanitizeInput($_POST['name']);
            $description = sanitizeInput($_POST['description']);
            $max_members = (int)$_POST['max_members'];
            
            $stmt = $pdo->prepare("UPDATE teams SET name = ?, description = ?, max_members = ? WHERE id = ? AND leader_id = ?");
            if ($stmt->execute([$name, $description, $max_members, $team_id, $userId])) {
                $success = 'Team updated successfully!';
            }
        }
    }
}

$stmt = $pdo->prepare("SELECT t.*, s.name as sport_name, (SELECT COUNT(*) FROM team_members WHERE team_id = t.id AND status = 'Approved') as member_count FROM teams t JOIN sports s ON t.sport_id = s.id WHERE t.leader_id = ? ORDER BY t.created_at DESC");
$stmt->execute([$userId]);
$teams = $stmt->fetchAll();

$csrfToken = generateCSRFToken();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Teams - Team Leader</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <nav class="navbar">
        <a href="../dashboard.php" class="logo">⚽ Sports Management</a>
        <ul class="nav-links">
            <li><a href="../dashboard.php">Dashboard</a></li>
            <li><a href="teams.php">My Teams</a></li>
            <li><a href="requests.php">Requests</a></li>
            <li><a href="../profile.php">Profile</a></li>
            <li><a href="../logout.php">Logout</a></li>
        </ul>
    </nav>

    <div class="container">
        <h1 class="fade-in" style="margin-bottom: 2rem;">My Teams</h1>
        
        <?php if ($success): ?>
            <div class="alert alert-success fade-in"><?= e($success) ?></div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="alert alert-danger fade-in"><?= e($error) ?></div>
        <?php endif; ?>
        
        <?php if (empty($teams)): ?>
            <div class="glass-card fade-in">
                <p style="opacity: 0.7;">You don't have any teams yet. Contact admin to create a team for you.</p>
            </div>
        <?php else: ?>
            <div class="grid grid-2">
                <?php foreach ($teams as $team): ?>
                    <div class="glass-card fade-in">
                        <h3 style="margin-bottom: 1rem;"><?= e($team['name']) ?></h3>
                        <p style="opacity: 0.8; margin-bottom: 0.5rem;"><strong>Sport:</strong> <?= e($team['sport_name']) ?></p>
                        <p style="opacity: 0.8; margin-bottom: 0.5rem;"><strong>Members:</strong> <?= e($team['member_count']) ?> / <?= e($team['max_members']) ?></p>
                        <p style="opacity: 0.8; margin-bottom: 1rem;"><strong>Description:</strong> <?= e($team['description']) ?></p>
                        
                        <div style="display: flex; gap: 0.5rem;">
                            <a href="team-members.php?id=<?= $team['id'] ?>" class="btn btn-sm btn-primary">View Members</a>
                            <button onclick="openEditModal(<?= $team['id'] ?>, '<?= e($team['name']) ?>', '<?= e($team['description']) ?>', <?= $team['max_members'] ?>)" class="btn btn-sm btn-success">Edit</button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <div id="editTeamModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Edit Team</h3>
                <button class="modal-close" onclick="closeModal('editTeamModal')">&times;</button>
            </div>
            <form method="POST">
                <input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>">
                <input type="hidden" name="action" value="update">
                <input type="hidden" name="team_id" id="edit_team_id">
                
                <div class="form-group">
                    <label>Team Name *</label>
                    <input type="text" name="name" id="edit_name" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" id="edit_description" class="form-control" rows="3"></textarea>
                </div>
                
                <div class="form-group">
                    <label>Max Members</label>
                    <input type="number" name="max_members" id="edit_max_members" class="form-control" min="5" max="100">
                </div>
                
                <button type="submit" class="btn btn-primary" style="width: 100%;">Update Team</button>
            </form>
        </div>
    </div>

    <footer>
        <p>Developed by ABID MEHMOOD | Phone: 03029382306</p>
    </footer>

    <script src="../assets/js/main.js"></script>
    <script>
        function openEditModal(id, name, description, maxMembers) {
            document.getElementById('edit_team_id').value = id;
            document.getElementById('edit_name').value = name;
            document.getElementById('edit_description').value = description;
            document.getElementById('edit_max_members').value = maxMembers;
            openModal('editTeamModal');
        }
    </script>
</body>
</html>
