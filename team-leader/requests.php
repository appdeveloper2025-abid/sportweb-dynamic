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
        $request_id = (int)$_POST['request_id'];
        
        // Verify team ownership
        $stmt = $pdo->prepare("SELECT tm.*, t.leader_id FROM team_members tm JOIN teams t ON tm.team_id = t.id WHERE tm.id = ?");
        $stmt->execute([$request_id]);
        $request = $stmt->fetch();
        
        if ($request && $request['leader_id'] == $userId) {
            if ($action === 'approve') {
                $stmt = $pdo->prepare("UPDATE team_members SET status = 'Approved' WHERE id = ?");
                if ($stmt->execute([$request_id])) {
                    $success = 'Request approved successfully!';
                }
            } elseif ($action === 'reject') {
                $stmt = $pdo->prepare("UPDATE team_members SET status = 'Rejected' WHERE id = ?");
                if ($stmt->execute([$request_id])) {
                    $success = 'Request rejected.';
                }
            }
        } else {
            $error = 'Unauthorized action.';
        }
    }
}

$stmt = $pdo->prepare("SELECT tm.*, t.name as team_name, u.name as user_name, u.email, u.phone, u.skill_level FROM team_members tm JOIN teams t ON tm.team_id = t.id JOIN users u ON tm.user_id = u.id WHERE t.leader_id = ? AND tm.status = 'Pending' ORDER BY tm.joined_at DESC");
$stmt->execute([$userId]);
$requests = $stmt->fetchAll();

$csrfToken = generateCSRFToken();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Join Requests - Team Leader</title>
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
        <h1 class="fade-in" style="margin-bottom: 2rem;">Join Requests</h1>
        
        <?php if ($success): ?>
            <div class="alert alert-success fade-in"><?= e($success) ?></div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="alert alert-danger fade-in"><?= e($error) ?></div>
        <?php endif; ?>
        
        <div class="glass-card fade-in">
            <?php if (empty($requests)): ?>
                <p style="opacity: 0.7;">No pending requests.</p>
            <?php else: ?>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Team</th>
                                <th>User</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Skill Level</th>
                                <th>Requested At</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($requests as $request): ?>
                                <tr>
                                    <td><?= e($request['team_name']) ?></td>
                                    <td><?= e($request['user_name']) ?></td>
                                    <td><?= e($request['email']) ?></td>
                                    <td><?= e($request['phone']) ?></td>
                                    <td><?= e($request['skill_level']) ?></td>
                                    <td><?= date('M d, Y H:i', strtotime($request['joined_at'])) ?></td>
                                    <td>
                                        <form method="POST" style="display: inline;">
                                            <input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>">
                                            <input type="hidden" name="action" value="approve">
                                            <input type="hidden" name="request_id" value="<?= $request['id'] ?>">
                                            <button type="submit" class="btn btn-sm btn-success">Approve</button>
                                        </form>
                                        <form method="POST" style="display: inline;">
                                            <input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>">
                                            <input type="hidden" name="action" value="reject">
                                            <input type="hidden" name="request_id" value="<?= $request['id'] ?>">
                                            <button type="submit" class="btn btn-sm btn-danger">Reject</button>
                                        </form>
                                    </td>
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
