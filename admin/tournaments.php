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
            $tournament_date = sanitizeInput($_POST['tournament_date']);
            $tournament_time = sanitizeInput($_POST['tournament_time']);
            $location = sanitizeInput($_POST['location']);
            $registration_deadline = sanitizeInput($_POST['registration_deadline']);
            $description = sanitizeInput($_POST['description']);
            
            if (empty($name) || !$sport_id || empty($tournament_date) || empty($location) || empty($registration_deadline)) {
                $error = 'All required fields must be filled.';
            } elseif (strtotime($registration_deadline) >= strtotime($tournament_date)) {
                $error = 'Registration deadline must be before tournament date.';
            } else {
                $stmt = $pdo->prepare("INSERT INTO tournaments (name, sport_id, tournament_date, tournament_time, location, registration_deadline, description, created_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                if ($stmt->execute([$name, $sport_id, $tournament_date, $tournament_time, $location, $registration_deadline, $description, $_SESSION['user_id']])) {
                    $success = 'Tournament created successfully!';
                } else {
                    $error = 'Failed to create tournament.';
                }
            }
        } elseif ($action === 'delete') {
            $id = (int)$_POST['id'];
            $stmt = $pdo->prepare("DELETE FROM tournaments WHERE id = ?");
            if ($stmt->execute([$id])) {
                $success = 'Tournament deleted successfully!';
            }
        }
    }
}

$stmt = $pdo->query("SELECT t.*, s.name as sport_name FROM tournaments t JOIN sports s ON t.sport_id = s.id ORDER BY t.tournament_date DESC");
$tournaments = $stmt->fetchAll();

$stmt = $pdo->query("SELECT id, name FROM sports ORDER BY name");
$sports = $stmt->fetchAll();

$csrfToken = generateCSRFToken();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Tournaments - Admin</title>
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
            <h1 class="fade-in">Manage Tournaments</h1>
            <button onclick="openModal('addTournamentModal')" class="btn btn-primary">Create Tournament</button>
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
                            <th>Tournament</th>
                            <th>Sport</th>
                            <th>Date & Time</th>
                            <th>Location</th>
                            <th>Deadline</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($tournaments as $tournament): ?>
                            <tr>
                                <td><?= e($tournament['id']) ?></td>
                                <td><?= e($tournament['name']) ?></td>
                                <td><?= e($tournament['sport_name']) ?></td>
                                <td><?= date('M d, Y', strtotime($tournament['tournament_date'])) ?> <?= date('h:i A', strtotime($tournament['tournament_time'])) ?></td>
                                <td><?= e($tournament['location']) ?></td>
                                <td><?= date('M d, Y H:i', strtotime($tournament['registration_deadline'])) ?></td>
                                <td>
                                    <?php if (strtotime($tournament['registration_deadline']) > time()): ?>
                                        <span class="badge badge-success">Open</span>
                                    <?php else: ?>
                                        <span class="badge badge-danger">Closed</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="tournament-applications.php?id=<?= $tournament['id'] ?>" class="btn btn-sm btn-primary">View Applications</a>
                                    <form method="POST" style="display: inline;" onsubmit="return confirm('Delete this tournament?')">
                                        <input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id" value="<?= $tournament['id'] ?>">
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

    <div id="addTournamentModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Create Tournament</h3>
                <button class="modal-close" onclick="closeModal('addTournamentModal')">&times;</button>
            </div>
            <form method="POST">
                <input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>">
                <input type="hidden" name="action" value="add">
                
                <div class="form-group">
                    <label>Tournament Name *</label>
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
                    <label>Tournament Date *</label>
                    <input type="date" name="tournament_date" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label>Tournament Time *</label>
                    <input type="time" name="tournament_time" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label>Location *</label>
                    <input type="text" name="location" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label>Registration Deadline *</label>
                    <input type="datetime-local" name="registration_deadline" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" class="form-control" rows="3"></textarea>
                </div>
                
                <button type="submit" class="btn btn-primary" style="width: 100%;">Create Tournament</button>
            </form>
        </div>
    </div>

    <footer>
        <p>Developed by ABID MEHMOOD | Phone: 03029382306</p>
    </footer>

    <script src="../assets/js/main.js"></script>
</body>
</html>
