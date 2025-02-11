<?php
session_start();
require_once '../inc/config.php';
require_once '../inc/db_config.php';
require_once 'admin_auth.php';

// Verifică autentificarea
requireAdminLogin();

// Preluăm toate meciurile
$query = "SELECT m.*, 
          ht.name as home_team_name, 
          at.name as away_team_name,
          l.name as league_name 
          FROM matches m 
          LEFT JOIN teams ht ON m.home_team_id = ht.id 
          LEFT JOIN teams at ON m.away_team_id = at.id 
          LEFT JOIN leagues l ON m.league = l.id 
          ORDER BY m.match_time DESC";
$result = $conn->query($query);

// Get statistics
$stats = [
    'total_matches' => 0,
    'today_matches' => 0,
    'live_matches' => 0,
    'upcoming_matches' => 0
];

// Get total matches
$query = "SELECT COUNT(*) as count FROM matches";
$count_result = mysqli_query($conn, $query);
if ($count_result) {
    $row = mysqli_fetch_assoc($count_result);
    $stats['total_matches'] = $row['count'];
}

// Get today's matches
$query = "SELECT COUNT(*) as count FROM matches WHERE DATE(match_time) = CURDATE()";
$count_result = mysqli_query($conn, $query);
if ($count_result) {
    $row = mysqli_fetch_assoc($count_result);
    $stats['today_matches'] = $row['count'];
}

// Get live matches
$query = "SELECT COUNT(*) as count FROM matches WHERE status = 'live'";
$count_result = mysqli_query($conn, $query);
if ($count_result) {
    $row = mysqli_fetch_assoc($count_result);
    $stats['live_matches'] = $row['count'];
}

// Get upcoming matches
$query = "SELECT COUNT(*) as count FROM matches WHERE match_time > NOW()";
$count_result = mysqli_query($conn, $query);
if ($count_result) {
    $row = mysqli_fetch_assoc($count_result);
    $stats['upcoming_matches'] = $row['count'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Matches - <?php echo $site_title; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css/admin.css">
</head>
<body>
    <div class="admin-dashboard">
        <div class="sidebar">
            <div class="logo">
                <img src="../images/logo.png" alt="Logo">
                <span>Admin Panel</span>
            </div>
            <nav>
                <a href="index.php">
                    <i class="fas fa-home"></i>
                    <span>Dashboard</span>
                </a>
                <a href="matches.php" class="active">
                    <i class="fas fa-futbol"></i>
                    <span>Matches</span>
                </a>
                <a href="teams.php">
                    <i class="fas fa-users"></i>
                    <span>Teams</span>
                </a>
                <a href="leagues.php">
                    <i class="fas fa-trophy"></i>
                    <span>Leagues</span>
                </a>
                <a href="settings.php">
                    <i class="fas fa-cog"></i>
                    <span>Settings</span>
                </a>
                <a href="logout.php">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Logout</span>
                </a>
            </nav>
        </div>

        <div class="main-content">
            <header>
                <div class="search-bar">
                    <i class="fas fa-search"></i>
                    <input type="text" placeholder="Search matches...">
                </div>
                <div class="user-menu">
                    <span>Welcome, Admin</span>
                    <img src="../images/avatar.png" alt="Admin">
                </div>
            </header>

            <div class="dashboard-stats">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-futbol"></i>
                    </div>
                    <div class="stat-details">
                        <h3>Total Matches</h3>
                        <p><?php echo number_format($stats['total_matches']); ?></p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon" style="background: var(--success);">
                        <i class="fas fa-calendar-day"></i>
                    </div>
                    <div class="stat-details">
                        <h3>Today's Matches</h3>
                        <p><?php echo number_format($stats['today_matches']); ?></p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon" style="background: var(--warning);">
                        <i class="fas fa-play"></i>
                    </div>
                    <div class="stat-details">
                        <h3>Live Matches</h3>
                        <p><?php echo number_format($stats['live_matches']); ?></p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon" style="background: var(--danger);">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="stat-details">
                        <h3>Upcoming Matches</h3>
                        <p><?php echo number_format($stats['upcoming_matches']); ?></p>
                    </div>
                </div>
            </div>

            <div class="content-card">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                    <h2>Manage Matches</h2>
                    <a href="add_match.php" class="btn btn-primary">
                        <i class="fas fa-plus"></i>
                        Add New Match
                    </a>
                </div>

                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Date & Time</th>
                            <th>League</th>
                            <th>Home Team</th>
                            <th>Away Team</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result && $result->num_rows > 0): ?>
                            <?php while ($match = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo date('d M Y H:i', strtotime($match['match_time'])); ?></td>
                                    <td><?php echo htmlspecialchars($match['league_name']); ?></td>
                                    <td><?php echo htmlspecialchars($match['home_team_name']); ?></td>
                                    <td><?php echo htmlspecialchars($match['away_team_name']); ?></td>
                                    <td>
                                        <?php
                                        $status_class = 'btn-primary';
                                        if ($match['status'] === 'live') {
                                            $status_class = 'btn-success';
                                        } elseif ($match['status'] === 'finished') {
                                            $status_class = 'btn-danger';
                                        }
                                        ?>
                                        <span class="btn <?php echo $status_class; ?>" style="font-size: 0.75rem; padding: 0.25rem 0.5rem;">
                                            <?php echo ucfirst($match['status']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div style="display: flex; gap: 0.5rem;">
                                            <a href="edit_match.php?id=<?php echo $match['id']; ?>" class="btn btn-warning">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="delete_match.php?id=<?php echo $match['id']; ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this match?')">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" style="text-align: center;">No matches found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        // Add search functionality
        document.querySelector('.search-bar input').addEventListener('input', function(e) {
            const searchText = e.target.value.toLowerCase();
            const rows = document.querySelectorAll('.data-table tbody tr');
            
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(searchText) ? '' : 'none';
            });
        });
    </script>
</body>
</html>