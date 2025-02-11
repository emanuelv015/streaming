<?php
session_start();
require_once '../inc/config.php';
require_once '../inc/db_config.php';
require_once 'admin_auth.php';

// Verifică autentificarea
requireAdminLogin();

// Get total matches
$query = "SELECT COUNT(*) as count FROM matches";
$result = mysqli_query($conn, $query);
$total_matches = $result->fetch_assoc()['count'];

// Get total teams
$query = "SELECT COUNT(*) as count FROM teams";
$result = mysqli_query($conn, $query);
$total_teams = $result->fetch_assoc()['count'];

// Get total leagues
$query = "SELECT COUNT(*) as count FROM leagues";
$result = mysqli_query($conn, $query);
$total_leagues = $result->fetch_assoc()['count'];

// Get today's matches
$today = date('Y-m-d');
$query = "SELECT COUNT(*) as count FROM matches WHERE DATE(date) = CURDATE()";
$result = mysqli_query($conn, $query);
$today_matches = $result->fetch_assoc()['count'];

// Get live matches
$query = "SELECT COUNT(*) as count FROM matches WHERE status = 'live'";
$result = mysqli_query($conn, $query);
$live_matches = $result->fetch_assoc()['count'];

// Get upcoming matches
$query = "SELECT COUNT(*) as count FROM matches WHERE DATE(date) > CURDATE()";
$result = mysqli_query($conn, $query);
$upcoming_matches = $result->fetch_assoc()['count'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - GetSportNews</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css/admin.css">
</head>
<body>
    <div class="admin-dashboard">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="logo">
                <img src="../images/logo.png" alt="Logo">
                <span>Admin Panel</span>
            </div>
            <nav>
                <a href="index.php" class="active">
                    <i class="fas fa-home"></i>
                    Dashboard
                </a>
                <a href="matches.php">
                    <i class="fas fa-futbol"></i>
                    Matches
                </a>
                <a href="teams.php">
                    <i class="fas fa-users"></i>
                    Teams
                </a>
                <a href="leagues.php">
                    <i class="fas fa-trophy"></i>
                    Leagues
                </a>
                <a href="settings.php">
                    <i class="fas fa-cog"></i>
                    Settings
                </a>
                <a href="logout.php">
                    <i class="fas fa-sign-out-alt"></i>
                    Logout
                </a>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <div class="top-bar">
                <div class="search-bar">
                    <input type="text" placeholder="Search matches...">
                    <i class="fas fa-search"></i>
                </div>
                <div class="user-info">
                    <span>Welcome, Admin</span>
                </div>
            </div>

            <div class="dashboard-stats">
                <div class="stat-card">
                    <i class="fas fa-futbol"></i>
                    <div class="stat-info">
                        <h3><?php echo $total_matches; ?></h3>
                        <p>Total Matches</p>
                    </div>
                </div>
                <div class="stat-card">
                    <i class="fas fa-users"></i>
                    <div class="stat-info">
                        <h3><?php echo $total_teams; ?></h3>
                        <p>Total Teams</p>
                    </div>
                </div>
                <div class="stat-card">
                    <i class="fas fa-trophy"></i>
                    <div class="stat-info">
                        <h3><?php echo $total_leagues; ?></h3>
                        <p>Total Leagues</p>
                    </div>
                </div>
                <div class="stat-card">
                    <i class="fas fa-calendar"></i>
                    <div class="stat-info">
                        <h3><?php echo $today_matches; ?></h3>
                        <p>Today's Matches</p>
                    </div>
                </div>
            </div>

            <div class="match-status">
                <div class="status-card">
                    <div class="icon">
                        <i class="fas fa-play"></i>
                    </div>
                    <div class="info">
                        <h4>Live Matches</h4>
                        <h2><?php echo $live_matches; ?></h2>
                    </div>
                </div>
                <div class="status-card">
                    <div class="icon">
                        <i class="fas fa-calendar-day"></i>
                    </div>
                    <div class="info">
                        <h4>Today's Matches</h4>
                        <h2><?php echo $today_matches; ?></h2>
                    </div>
                </div>
                <div class="status-card">
                    <div class="icon">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <div class="info">
                        <h4>Upcoming Matches</h4>
                        <h2><?php echo $upcoming_matches; ?></h2>
                    </div>
                </div>
            </div>

            <div class="quick-actions">
                <h2>Quick Actions</h2>
                <div class="action-buttons">
                    <a href="#" class="action-btn" onclick="openModal('addMatchModal')">
                        <i class="fas fa-plus"></i>
                        <span>Add Match</span>
                    </a>
                    <a href="#" class="action-btn" onclick="openModal('addTeamModal')">
                        <i class="fas fa-users"></i>
                        <span>Add Team</span>
                    </a>
                    <a href="#" class="action-btn" onclick="openModal('addLeagueModal')">
                        <i class="fas fa-trophy"></i>
                        <span>Add League</span>
                    </a>
                </div>
            </div>

            <!-- Add Match Modal -->
            <div id="addMatchModal" class="modal">
                <div class="modal-content">
                    <div class="modal-header">
                        <h2>Add New Match</h2>
                        <span class="close" onclick="closeModal('addMatchModal')">&times;</span>
                    </div>
                    <form id="addMatchForm" onsubmit="event.preventDefault(); submitForm('addMatchForm', 'actions/add_match.php')">
                        <div class="form-group">
                            <label>Home Team</label>
                            <select name="home_team_id" required>
                                <?php
                                $teams = $conn->query("SELECT id, name FROM teams ORDER BY name");
                                while ($team = $teams->fetch_assoc()) {
                                    echo "<option value='{$team['id']}'>{$team['name']}</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Away Team</label>
                            <select name="away_team_id" required>
                                <?php
                                $teams->data_seek(0);
                                while ($team = $teams->fetch_assoc()) {
                                    echo "<option value='{$team['id']}'>{$team['name']}</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>League</label>
                            <select name="league_id" required>
                                <?php
                                $leagues = $conn->query("SELECT id, name FROM leagues ORDER BY name");
                                while ($league = $leagues->fetch_assoc()) {
                                    echo "<option value='{$league['id']}'>{$league['name']}</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Match Time</label>
                            <input type="datetime-local" name="match_time" required>
                        </div>
                        <div class="form-group">
                            <label>Stream URL</label>
                            <input type="text" name="stream_url" required>
                        </div>
                        <button type="submit" class="btn-primary">Add Match</button>
                    </form>
                </div>
            </div>

            <!-- Add Team Modal -->
            <div id="addTeamModal" class="modal">
                <div class="modal-content">
                    <div class="modal-header">
                        <h2>Add New Team</h2>
                        <span class="close" onclick="closeModal('addTeamModal')">&times;</span>
                    </div>
                    <form id="addTeamForm" onsubmit="event.preventDefault(); submitForm('addTeamForm', 'actions/add_team.php')">
                        <div class="form-group">
                            <label>Team Name</label>
                            <input type="text" name="name" required>
                        </div>
                        <div class="form-group">
                            <label>Logo URL</label>
                            <input type="url" name="logo_url">
                        </div>
                        <button type="submit" class="btn-primary">Add Team</button>
                    </form>
                </div>
            </div>

            <!-- Add League Modal -->
            <div id="addLeagueModal" class="modal">
                <div class="modal-content">
                    <div class="modal-header">
                        <h2>Add New League</h2>
                        <span class="close" onclick="closeModal('addLeagueModal')">&times;</span>
                    </div>
                    <form id="addLeagueForm" onsubmit="event.preventDefault(); submitForm('addLeagueForm', 'actions/add_league.php')">
                        <div class="form-group">
                            <label>League Name</label>
                            <input type="text" name="name" required>
                        </div>
                        <div class="form-group">
                            <label>Flag URL</label>
                            <input type="url" name="flag_url">
                        </div>
                        <button type="submit" class="btn-primary">Add League</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="js/admin.js"></script>
</body>
</html>
