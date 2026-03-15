<?php
require_once '../config/database.php';
require_once '../includes/security.php';
startSecureSession();
requireRole('Admin');

// Get comprehensive statistics
$stats = [];

$stmt = $pdo->query("SELECT COUNT(*) as count FROM users WHERE role = 'Student'");
$stats['total_students'] = $stmt->fetch()['count'];

$stmt = $pdo->query("SELECT COUNT(*) as count FROM students");
$stats['registered_students'] = $stmt->fetch()['count'];

$stmt = $pdo->query("SELECT COUNT(*) as count FROM teams");
$stats['total_teams'] = $stmt->fetch()['count'];

$stmt = $pdo->query("SELECT COUNT(*) as count FROM tournaments");
$stats['total_tournaments'] = $stmt->fetch()['count'];

$stmt = $pdo->query("SELECT COUNT(*) as count FROM matches");
$stats['total_matches'] = $stmt->fetch()['count'];

$stmt = $pdo->query("SELECT COUNT(*) as count FROM matches WHERE status = 'Completed'");
$stats['completed_matches'] = $stmt->fetch()['count'];

$stmt = $pdo->query("SELECT COUNT(*) as count FROM tournaments WHERE status = 'Ongoing'");
$stats['ongoing_tournaments'] = $stmt->fetch()['count'];

// Get sports distribution
$stmt = $pdo->query("SELECT s.name, COUNT(t.id) as team_count FROM sports s LEFT JOIN teams t ON s.id = t.sport_id GROUP BY s.id ORDER BY team_count DESC LIMIT 5");
$sports_data = $stmt->fetchAll();

// Get institution distribution
$stmt = $pdo->query("SELECT institution_type, COUNT(*) as count FROM students GROUP BY institution_type");
$institution_data = $stmt->fetchAll();

// Get recent matches
$stmt = $pdo->query("SELECT m.*, t1.name as team1_name, t2.name as team2_name, s.name as sport_name, tour.name as tournament_name 
    FROM matches m 
    JOIN teams t1 ON m.team1_id = t1.id 
    JOIN teams t2 ON m.team2_id = t2.id 
    JOIN tournaments tour ON m.tournament_id = tour.id 
    JOIN sports s ON tour.sport_id = s.id 
    ORDER BY m.match_date DESC, m.match_time DESC LIMIT 5");
$recent_matches = $stmt->fetchAll();

// Get top teams
$stmt = $pdo->query("SELECT t.*, s.name as sport_name FROM teams t JOIN sports s ON t.sport_id = s.id ORDER BY t.points DESC, t.wins DESC LIMIT 5");
$top_teams = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - KPK Sports</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <nav class="navbar">
        <a href="../dashboard.php" class="logo">🏆 KPK Sports Management</a>
        <ul class="nav-links">
            <li><a href="../dashboard.php">Dashboard</a></li>
            <li><a href="students/manage.php">Students</a></li>
            <li><a href="teams.php">Teams</a></li>
            <li><a href="tournaments.php">Tournaments</a></li>
            <li><a href="matches/manage.php">Matches</a></li>
            <li><a href="statistics/view.php">Statistics</a></li>
            <li><a href="../logout.php">Logout</a></li>
        </ul>
    </nav>

    <div class="container">
        <h1 class="fade-in" style="text-shadow: 2px 2px 4px rgba(0,0,0,0.5);">Admin Dashboard - KPK Province</h1>
        
        <!-- Statistics Cards -->
        <div class="stats-grid fade-in">
            <div class="stat-card">
                <h3 data-stat="total_students"><?= $stats['total_students'] ?></h3>
                <p>Total Students</p>
            </div>
            <div class="stat-card">
                <h3 data-stat="total_teams"><?= $stats['total_teams'] ?></h3>
                <p>Total Teams</p>
            </div>
            <div class="stat-card">
                <h3 data-stat="total_tournaments"><?= $stats['total_tournaments'] ?></h3>
                <p>Tournaments</p>
            </div>
            <div class="stat-card">
                <h3 data-stat="total_matches"><?= $stats['total_matches'] ?></h3>
                <p>Total Matches</p>
            </div>
            <div class="stat-card">
                <h3 data-stat="ongoing_tournaments"><?= $stats['ongoing_tournaments'] ?></h3>
                <p>Ongoing Tournaments</p>
            </div>
            <div class="stat-card">
                <h3 data-stat="completed_matches"><?= $stats['completed_matches'] ?></h3>
                <p>Completed Matches</p>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="grid grid-2" style="margin-top: 2rem;">
            <div class="glass-card fade-in">
                <h3 style="margin-bottom: 1.5rem; text-shadow: 1px 1px 2px rgba(0,0,0,0.3);">Sports Distribution</h3>
                <canvas id="sportsChart" height="250"></canvas>
            </div>
            
            <div class="glass-card fade-in">
                <h3 style="margin-bottom: 1.5rem; text-shadow: 1px 1px 2px rgba(0,0,0,0.3);">Institution Types</h3>
                <canvas id="institutionChart" height="250"></canvas>
            </div>
        </div>

        <!-- Recent Matches -->
        <div class="glass-card fade-in" style="margin-top: 2rem;">
            <h3 style="margin-bottom: 1.5rem; text-shadow: 1px 1px 2px rgba(0,0,0,0.3);">Recent Matches</h3>
            <?php if (empty($recent_matches)): ?>
                <p style="opacity: 0.8;">No matches scheduled yet.</p>
            <?php else: ?>
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
                            <?php foreach ($recent_matches as $match): ?>
                                <tr>
                                    <td><?= e($match['tournament_name']) ?></td>
                                    <td><?= e($match['team1_name']) ?> vs <?= e($match['team2_name']) ?></td>
                                    <td><?= $match['team1_score'] ?> - <?= $match['team2_score'] ?></td>
                                    <td><?= date('M d, Y', strtotime($match['match_date'])) ?></td>
                                    <td><span class="badge badge-<?= $match['status'] === 'Completed' ? 'success' : 'warning' ?>"><?= e($match['status']) ?></span></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>

        <!-- Top Teams -->
        <div class="glass-card fade-in" style="margin-top: 2rem;">
            <h3 style="margin-bottom: 1.5rem; text-shadow: 1px 1px 2px rgba(0,0,0,0.3);">Top Teams</h3>
            <?php if (empty($top_teams)): ?>
                <p style="opacity: 0.8;">No teams registered yet.</p>
            <?php else: ?>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Rank</th>
                                <th>Team</th>
                                <th>Sport</th>
                                <th>Institution</th>
                                <th>W-D-L</th>
                                <th>Points</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $rank = 1; foreach ($top_teams as $team): ?>
                                <tr>
                                    <td><?= $rank++ ?></td>
                                    <td><?= e($team['name']) ?></td>
                                    <td><?= e($team['sport_name']) ?></td>
                                    <td><?= e($team['institution_name']) ?></td>
                                    <td><?= $team['wins'] ?>-<?= $team['draws'] ?>-<?= $team['losses'] ?></td>
                                    <td><strong><?= $team['points'] ?></strong></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <footer>
        <p style="text-shadow: 1px 1px 2px rgba(0,0,0,0.3);">Developed by ABID MEHMOOD | Phone: 03029382306</p>
    </footer>

    <script src="../assets/js/main.js"></script>
    <script>
        // Sports Distribution Chart
        const sportsCtx = document.getElementById('sportsChart').getContext('2d');
        new Chart(sportsCtx, {
            type: 'bar',
            data: {
                labels: <?= json_encode(array_column($sports_data, 'name')) ?>,
                datasets: [{
                    label: 'Number of Teams',
                    data: <?= json_encode(array_column($sports_data, 'team_count')) ?>,
                    backgroundColor: 'rgba(99, 102, 241, 0.7)',
                    borderColor: 'rgba(99, 102, 241, 1)',
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: { beginAtZero: true, ticks: { color: '#fff' }, grid: { color: 'rgba(255,255,255,0.1)' } },
                    x: { ticks: { color: '#fff' }, grid: { color: 'rgba(255,255,255,0.1)' } }
                }
            }
        });

        // Institution Distribution Chart
        const institutionCtx = document.getElementById('institutionChart').getContext('2d');
        new Chart(institutionCtx, {
            type: 'doughnut',
            data: {
                labels: <?= json_encode(array_column($institution_data, 'institution_type')) ?>,
                datasets: [{
                    data: <?= json_encode(array_column($institution_data, 'count')) ?>,
                    backgroundColor: [
                        'rgba(99, 102, 241, 0.7)',
                        'rgba(139, 92, 246, 0.7)',
                        'rgba(16, 185, 129, 0.7)'
                    ],
                    borderWidth: 2,
                    borderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { labels: { color: '#fff' } }
                }
            }
        });
    </script>
</body>
</html>
