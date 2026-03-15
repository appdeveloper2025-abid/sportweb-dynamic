<?php
require_once '../config/database.php';
require_once '../includes/security.php';
startSecureSession();
requireRole('Admin');

$success = '';
$error = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $error = 'Invalid security token.';
    } else {
        $action = $_POST['action'] ?? '';
        
        if ($action === 'add') {
            $name = sanitizeInput($_POST['name']);
            $description = sanitizeInput($_POST['description']);
            
            if (empty($name)) {
                $error = 'Sport name is required.';
            } else {
                $stmt = $pdo->prepare("INSERT INTO sports (name, description, created_by) VALUES (?, ?, ?)");
                if ($stmt->execute([$name, $description, $_SESSION['user_id']])) {
                    $success = 'Sport added successfully!';
                } else {
                    $error = 'Failed to add sport.';
                }
            }
        } elseif ($action === 'delete') {
            $id = (int)$_POST['id'];
            $stmt = $pdo->prepare("DELETE FROM sports WHERE id = ?");
            if ($stmt->execute([$id])) {
                $success = 'Sport deleted successfully!';
            } else {
                $error = 'Failed to delete sport.';
            }
        }
    }
}

// Get all sports
$stmt = $pdo->query("SELECT s.*, u.name as creator_name FROM sports s LEFT JOIN users u ON s.created_by = u.id ORDER BY s.created_at DESC");
$sports = $stmt->fetchAll();

$csrfToken = generateCSRFToken();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Sports - Admin</title>
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
            <h1 class="fade-in">Manage Sports</h1>
            <button onclick="openModal('addSportModal')" class="btn btn-primary">Add Sport</button>
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
                            <th>Name</th>
                            <th>Description</th>
                            <th>Created By</th>
                            <th>Created At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($sports as $sport): ?>
                            <tr>
                                <td><?= e($sport['id']) ?></td>
                                <td><?= e($sport['name']) ?></td>
                                <td><?= e($sport['description']) ?></td>
                                <td><?= e($sport['creator_name']) ?></td>
                                <td><?= date('M d, Y', strtotime($sport['created_at'])) ?></td>
                                <td>
                                    <form method="POST" style="display: inline;" onsubmit="return confirm('Are you sure?')">
                                        <input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id" value="<?= $sport['id'] ?>">
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

    <!-- Add Sport Modal -->
    <div id="addSportModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Add New Sport</h3>
                <button class="modal-close" onclick="closeModal('addSportModal')">&times;</button>
            </div>
            <form method="POST">
                <input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>">
                <input type="hidden" name="action" value="add">
                
                <div class="form-group">
                    <label>Sport Name *</label>
                    <input type="text" name="name" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" class="form-control" rows="3"></textarea>
                </div>
                
                <button type="submit" class="btn btn-primary" style="width: 100%;">Add Sport</button>
            </form>
        </div>
    </div>

    <footer>
        <p>Developed by ABID MEHMOOD | Phone: 03029382306</p>
    </footer>

    <script src="../assets/js/main.js"></script>
</body>
</html>
