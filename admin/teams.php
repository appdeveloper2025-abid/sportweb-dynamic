<?php
require_once '../config/database.php';
require_once '../includes/security.php';
startSecureSession();
requireRole('Admin');

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $error = 'Invalid security token.';
    } else {
        $action = $_POST['action'] ?? '';
        
        if ($action === 'add') {
            $name = sanitizeInput($_POST['name']);
            $sport_id = (int)$_POST['sport_id'];
            $leader_id = (int)$_POST['leader_id'];
            $description = sanitizeInput($_POST['description']);
            $max_members = (int)$_POST['max_members'];
            
            if (empty($name) || !$sport_id || !$leader_id) {
                $error = 'All required fields must be filled.';
            } else {
                $stmt = $pdo->prepare("INSERT INTO teams (name, sport_id, leader_id, description, max_members) VALUES (?, ?, ?, ?, ?)");
                if ($stmt->execute([$name, $sport_id, $leader_id, $description, $max_members])) {
                    $success = 'Team created successfully!';
                } else {
                    $error = 'Failed to create team.';
                }
            }
        } elseif ($action === 'delete') {
            $id = (int)$_POST['id'];
            $stmt = $pdo->prepare("DELETE FROM teams WHERE id = ?");
            if ($stmt->execute([$id])) {
                $success = 'Team deleted successfully!';
            }
        }
    }
}

$stmt = $pdo->query("SELECT t.*, s.name as sport_name, u.name as leader_name FROM teams t JOIN sports s ON t.sport_id = s.id JOIN users u ON t.leader_id = u.id ORDER BY t.created_at DESC");
$teams = $stmt->fetchAll();

$stmt = $pdo->query("SELECT id, name FROM sports ORDER BY name");
$sports = $stmt->fetchAll();

$stmt = $pdo->query("SELECT id, name FROM users WHERE role IN ('Team Leader', 'Admin') ORDER BY name");
$leaders = $stmt->fetchAll();

$csrfToken = generateCSRFToken();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Teams - Admin</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <nav class="navbar">
        <a href="../dashboard.php" class="logo">⚽ Sports Management</a>
        <ul class="nav-links">
            <li><a href="../dashboard.php">Dashboard</a></li>
            <li><a href="sports.php">Sports</a></li>
            <li><a href="teams.php">Teams</a></li>
            <li><a href="tournaments.php">Tournaments</a></li>
            <li><a href="users.php">Users</a></li>
            <li><a href="../logout.php">Logout</a></li>
        </ul>
    </nav>

    <div class="container">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
            <h1 class="fade-in">Manage Teams</h1>
            <button onclick="openModal('addTeamModal')" class="btn btn-primary">Create Team</button>
        </div>
        
        <?php if ($success): ?>
            <div class="alert alert-success fade-in"><?= e($success) ?></div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="alert alert-danger fade-in"><?= e($error) ?></div>
        <?php endif; ?>
        
        <div class="glass-card fade-in">
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Team Name</th>
                            <th>Sport</th>
                            <th>Leader</th>
                            <th>Max Members</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($teams as $team): ?>
                            <tr>
                                <td><?= e($team['id']) ?></td>
                                <td><?= e($team['name']) ?></td>
                                <td><?= e($team['sport_name']) ?></td>
                                <td><?= e($team['leader_name']) ?></td>
                                <td><?= e($team['max_members']) ?></td>
                                <td><?= date('M d, Y', strtotime($team['created_at'])) ?></td>
                                <td>
                                    <form method="POST" style="display: inline;" onsubmit="return confirm('Delete this team?')">
                                        <input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id" value="<?= $team['id'] ?>">
                                        <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div id="addTeamModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Create New Team</h3>
                <button class="modal-close" onclick="closeModal('addTeamModal')">&times;</button>
            </div>
            <form method="POST">
                <input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>">
                <input type="hidden" name="action" value="add">
                
                <div class="form-group">
                    <label>Team Name *</label>
                    <input type="text" name="name" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label>Sport *</label>
                    <select name="sport_id" class="form-control" required>
                        <option value="">Select Sport</option>
                        <?php foreach ($sports as $sport): ?>
                            <option value="<?= $sport['id'] ?>"><?= e($sport['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Team Leader *</label>
                    <select name="leader_id" class="form-control" required>
                        <option value="">Select Leader</option>
                        <?php foreach ($leaders as $leader): ?>
                            <option value="<?= $leader['id'] ?>"><?= e($leader['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" class="form-control" rows="3"></textarea>
                </div>
                
                <div class="form-group">
                    <label>Max Members</label>
                    <input type="number" name="max_members" class="form-control" value="20" min="5" max="100">
                </div>
                
                <button type="submit" class="btn btn-primary" style="width: 100%;">Create Team</button>
            </form>
        </div>
    </div>

    <footer>
        <p>Developed by ABID MEHMOOD | Phone: 03029382306</p>
    </footer>

    <script src="../assets/js/main.js"></script>
</body>
</html>
