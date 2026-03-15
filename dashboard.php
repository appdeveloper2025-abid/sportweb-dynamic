<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

require_once 'config/database.php';

$userName = $_SESSION['user_name'];
$userRole = $_SESSION['user_role'];

$stats = [];
$stmt = $pdo->query("SELECT COUNT(*) as count FROM users WHERE role = 'Student'");
$stats['students'] = $stmt->fetch()['count'];

$stmt = $pdo->query("SELECT COUNT(*) as count FROM teams");
$stats['teams'] = $stmt->fetch()['count'];

$stmt = $pdo->query("SELECT COUNT(*) as count FROM tournaments");
$stats['tournaments'] = $stmt->fetch()['count'];

$stmt = $pdo->query("SELECT COUNT(*) as count FROM matches");
$stats['matches'] = $stmt->fetch()['count'];

$stmt = $pdo->query("SELECT s.name, COUNT(t.id) as count FROM sports s LEFT JOIN teams t ON s.id = t.sport_id GROUP BY s.id");
$sports_data = $stmt->fetchAll();

$stmt = $pdo->query("SELECT m.*, t1.name as team1, t2.name as team2, tour.name as tournament 
    FROM matches m 
    JOIN teams t1 ON m.team1_id = t1.id 
    JOIN teams t2 ON m.team2_id = t2.id 
    JOIN tournaments tour ON m.tournament_id = tour.id 
    ORDER BY m.match_date DESC LIMIT 5");
$matches = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - KPK Sports</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <nav class="navbar slide-in-down">
        <a href="dashboard.php" class="logo">🏆 KPK Sports Management</a>
        <ul class="nav-links">
            <li><a href="dashboard.php">Dashboard</a></li>
            <?php if ($userRole === 'Admin'): ?>
                <li><a href="admin/sports.php">Sports</a></li>
                <li><a href="admin/teams.php">Teams</a></li>
                <li><a href="admin/tournaments.php">Tournaments</a></li>
                <li><a href="admin/matches.php">Matches</a></li>
            <?php endif; ?>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </nav>

    <div class="container">
        <h1 class="zoom-in" style="text-shadow: 2px 2px 4px rgba(0,0,0,0.5);">Welcome, <?= htmlspecialchars($userName) ?>!</h1>
        <p style="opacity: 0.9; margin-bottom: 2rem; text-shadow: 1px 1px 2px rgba(0,0,0,0.3);" class="fade-in" style="animation-delay: 0.1s;">
            Role: <span class="badge badge-primary pulse"><?= htmlspecialchars($userRole) ?></span>
        </p>
        
        <div class="stats-grid">
            <div class="stat-card slide-in-left" style="animation-delay: 0.1s;">
                <h3><?= $stats['students'] ?></h3>
                <p>Total Students</p>
            </div>
            <div class="stat-card slide-in-up" style="animation-delay: 0.2s;">
                <h3><?= $stats['teams'] ?></h3>
                <p>Total Teams</p>
            </div>
            <div class="stat-card slide-in-up" style="animation-delay: 0.3s;">
                <h3><?= $stats['tournaments'] ?></h3>
                <p>Tournaments</p>
            </div>
            <div class="stat-card slide-in-right" style="animation-delay: 0.4s;">
                <h3><?= $stats['matches'] ?></h3>
                <p>Total Matches</p>
            </div>
        </div>

        <div class="glass-card zoom-in" style="margin-top: 2rem; animation-delay: 0.5s;">
            <h3 style="margin-bottom: 1.5rem; text-shadow: 1px 1px 2px rgba(0,0,0,0.3);">📊 Sports Distribution</h3>
            <canvas id="sportsChart" height="100"></canvas>
        </div>

        <?php if (!empty($matches)): ?>
        <div class="glass-card slide-in-up" style="margin-top: 2rem; animation-delay: 0.6s;">
            <h3 style="margin-bottom: 1.5rem; text-shadow: 1px 1px 2px rgba(0,0,0,0.3);">⚽ Recent Matches</h3>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Tournament</th>
                            <th>Match</th>
                            <th>Score</th>
                            <th>Date</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($matches as $match): ?>
                            <tr class="fade-in">
                                <td><?= htmlspecialchars($match['tournament']) ?></td>
                                <td><?= htmlspecialchars($match['team1']) ?> vs <?= htmlspecialchars($match['team2']) ?></td>
                                <td><?= $match['team1_score'] ?> - <?= $match['team2_score'] ?></td>
                                <td><?= date('M d, Y', strtotime($match['match_date'])) ?></td>
                                <td><span class="badge badge-<?= $match['status'] === 'Completed' ? 'success' : 'warning' ?>"><?= $match['status'] ?></span></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <footer class="slide-in-up" style="animation-delay: 0.7s;">
        <p style="text-shadow: 1px 1px 2px rgba(0,0,0,0.3);">Developed by ABID MEHMOOD | Phone: 03029382306</p>
    </footer>

    <script>
        const ctx = document.getElementById('sportsChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: <?= json_encode(array_column($sports_data, 'name')) ?>,
                datasets: [{
                    label: 'Number of Teams',
                    data: <?= json_encode(array_column($sports_data, 'count')) ?>,
                    backgroundColor: 'rgba(99, 102, 241, 0.7)',
                    borderColor: 'rgba(99, 102, 241, 1)',
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                animation: {
                    duration: 2000,
                    easing: 'easeInOutQuart'
                },
                plugins: { legend: { labels: { color: '#fff' } } },
                scales: {
                    y: { beginAtZero: true, ticks: { color: '#fff' }, grid: { color: 'rgba(255,255,255,0.1)' } },
                    x: { ticks: { color: '#fff' }, grid: { color: 'rgba(255,255,255,0.1)' } }
                }
            }
        });
    </script>
</body>
</html>
