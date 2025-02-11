<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Get the document root path
$root_path = $_SERVER['DOCUMENT_ROOT'];
require_once($root_path . '/admin/auth.php');
requireLogin();

require_once '../inc/db_config.php';

// Statistici pentru ultima zi
$today = date('Y-m-d');
$yesterday = date('Y-m-d', strtotime('-1 day'));

// Adaugă în secțiunea de statistici
$date_range = $_GET['range'] ?? 'today';
$end_date = date('Y-m-d');
$start_date = $end_date;

switch($date_range) {
    case 'week':
        $start_date = date('Y-m-d', strtotime('-7 days'));
        break;
    case 'month':
        $start_date = date('Y-m-d', strtotime('-30 days'));
        break;
    case 'custom':
        $start_date = $_GET['start_date'] ?? $end_date;
        $end_date = $_GET['end_date'] ?? $end_date;
        break;
}

// Adaugă această funcție pentru debugging
function executeQuery($conn, $query, $params = [], $types = '') {
    $stmt = $conn->prepare($query);
    if ($stmt === false) {
        error_log("Prepare failed: " . $conn->error);
        return false;
    }
    
    if (!empty($params)) {
        if (!$stmt->bind_param($types, ...$params)) {
            error_log("Binding parameters failed: " . $stmt->error);
            return false;
        }
    }
    
    if (!$stmt->execute()) {
        error_log("Execute failed: " . $stmt->error);
        return false;
    }
    
    return $stmt->get_result();
}

// Vizite astăzi
$result = executeQuery($conn, 
    "SELECT 
        COUNT(*) as total_visits,
        COUNT(DISTINCT session_id) as unique_visitors,
        AVG(time_spent) as avg_time
    FROM user_visits 
    WHERE DATE(created_at) = ?",
    [$today],
    's'
);
$today_stats = $result ? $result->fetch_assoc() : ['total_visits' => 0, 'unique_visitors' => 0, 'avg_time' => 0];

// Top meciuri live
$stmt = $conn->prepare("
    SELECT 
        m.id,
        h.name as home_team,
        a.name as away_team,
        l.name as league_name,
        m.status,
        COUNT(ua.id) as views
    FROM matches m
    LEFT JOIN teams h ON m.home_team = h.id
    LEFT JOIN teams a ON m.away_team = a.id
    LEFT JOIN leagues l ON m.league = l.id
    LEFT JOIN user_actions ua ON m.id = ua.match_id
    WHERE m.status = 'live'
    GROUP BY m.id
    ORDER BY views DESC
");
$stmt->execute();
$live_matches = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Dispozitive folosite
$stmt = $conn->prepare("
    SELECT 
        device_type,
        COUNT(*) as count
    FROM user_visits
    WHERE DATE(created_at) = ?
    GROUP BY device_type
");
$stmt->bind_param("s", $today);
$stmt->execute();
$devices = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Top meciuri după vizualizări
$stmt = $conn->prepare("
    SELECT 
        m.id,
        h.name as home_team,
        a.name as away_team,
        l.name as league_name,
        s.views,
        s.unique_viewers,
        s.peak_viewers,
        s.avg_watch_time
    FROM matches m
    LEFT JOIN teams h ON m.home_team = h.id
    LEFT JOIN teams a ON m.away_team = a.id
    LEFT JOIN leagues l ON m.league = l.id
    LEFT JOIN stream_stats s ON m.id = s.match_id
    WHERE m.status = 'live'
    ORDER BY s.views DESC
    LIMIT 5
");
$stmt->execute();
$top_matches = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Statistici în timp real
$stmt = $conn->prepare("
    SELECT 
        COUNT(*) as current_viewers,
        COUNT(DISTINCT session_id) as unique_viewers
    FROM user_visits
    WHERE page_url LIKE '%stream.php%'
    AND TIMESTAMPDIFF(MINUTE, created_at, NOW()) <= 5
");
$stmt->execute();
$realtime_stats = $stmt->get_result()->fetch_assoc();

// Statistici istorice
$result = executeQuery($conn,
    "SELECT 
        m.id,
        h.name as home_team,
        a.name as away_team,
        l.name as league_name,
        SUM(sh.total_views) as total_views,
        AVG(sh.unique_viewers) as avg_unique_viewers,
        MAX(sh.peak_viewers) as max_peak_viewers,
        AVG(sh.avg_watch_time) as avg_watch_time
    FROM matches m
    LEFT JOIN teams h ON m.home_team = h.id
    LEFT JOIN teams a ON m.away_team = a.id
    LEFT JOIN leagues l ON m.league = l.id
    LEFT JOIN stats_history sh ON m.id = sh.match_id
    WHERE sh.date BETWEEN ? AND ?
    GROUP BY m.id
    ORDER BY total_views DESC
    LIMIT 10",
    [$start_date, $end_date],
    'ss'
);
$historic_stats = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .sidebar {
            min-height: 100vh;
            background: #343a40;
            padding-top: 20px;
        }
        .sidebar a {
            color: #fff;
            text-decoration: none;
            padding: 10px 15px;
            display: block;
        }
        .sidebar a:hover {
            background: #495057;
        }
        .content {
            padding: 20px;
        }
        .nav-link.active {
            background: #495057;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-2 sidebar">
                <h4 class="text-white text-center mb-4">Admin Panel</h4>
                <nav>
                    <a href="index.php" class="nav-link active">
                        <i class="bi bi-house-door"></i> Dashboard
                    </a>
                    <a href="matches.php" class="nav-link">
                        <i class="bi bi-controller"></i> Matches
                    </a>
                    <a href="leagues.php" class="nav-link">
                        <i class="bi bi-trophy"></i> Leagues
                    </a>
                    <a href="teams.php" class="nav-link">
                        <i class="bi bi-people"></i> Teams
                    </a>
                    <a href="logout.php" class="nav-link text-danger mt-5">
                        <i class="bi bi-box-arrow-right"></i> Logout
                    </a>
                </nav>
            </div>

            <!-- Main Content -->
            <div class="col-md-10 content">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2>Dashboard</h2>
                    <span>Welcome, <?php echo htmlspecialchars($_SESSION['admin_username']); ?></span>
                </div>

                <!-- Înlocuiește bannerul existent cu acesta -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card bg-dark text-white">
                            <div class="card-body d-flex align-items-center justify-content-between" style="background: linear-gradient(45deg, #343a40, #1a1a1a);">
                                <div class="d-flex align-items-center gap-4">
                                    <div style="background: #ffc107; padding: 15px; border-radius: 12px;">
                                        <i class="bi bi-phone-fill" style="font-size: 32px; color: #000;"></i>
                                    </div>
                                    <div>
                                        <h4 class="mb-2">Add to Home Screen</h4>
                                        <p class="mb-0 text-light" style="opacity: 0.8;">
                                            <span class="android-instructions">
                                                Open Chrome menu (⋮) and tap "Add to Home screen"
                                            </span>
                                            <span class="ios-instructions" style="display: none;">
                                                Tap Share button and choose "Add to Home Screen"
                                            </span>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Stats -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card text-white bg-primary">
                            <div class="card-body">
                                <h5 class="card-title">Today's Visits</h5>
                                <h2><?php echo number_format($today_stats['total_visits']); ?></h2>
                                <p class="mb-0">Unique: <?php echo number_format($today_stats['unique_visitors']); ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-white bg-success">
                            <div class="card-body">
                                <h5 class="card-title">Live Matches</h5>
                                <h2><?php echo count($live_matches); ?></h2>
                                <p class="mb-0">Active streams</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-white bg-info">
                            <div class="card-body">
                                <h5 class="card-title">Avg. Time on Site</h5>
                                <h2><?php echo round($today_stats['avg_time'] / 60, 1); ?> min</h2>
                                <p class="mb-0">Per session today</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-white bg-warning">
                            <div class="card-body">
                                <h5 class="card-title">Mobile Users</h5>
                                <?php
                                $mobile_users = 0;
                                foreach ($devices as $device) {
                                    if ($device['device_type'] == 'phone' || $device['device_type'] == 'tablet') {
                                        $mobile_users += $device['count'];
                                    }
                                }
                                $total_users = array_sum(array_column($devices, 'count'));
                                $mobile_percentage = $total_users > 0 ? round(($mobile_users / $total_users) * 100) : 0;
                                ?>
                                <h2><?php echo $mobile_percentage; ?>%</h2>
                                <p class="mb-0">Of total traffic</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Live Matches Table -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Live Matches</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Match</th>
                                        <th>League</th>
                                        <th>Views</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($live_matches as $match): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($match['home_team'] . ' vs ' . $match['away_team']); ?></td>
                                        <td><?php echo htmlspecialchars($match['league_name']); ?></td>
                                        <td><?php echo number_format($match['views']); ?></td>
                                        <td>
                                            <form method="POST" action="matches.php" style="display: inline;">
                                                <input type="hidden" name="match_id" value="<?php echo $match['id']; ?>">
                                                <select name="status" onchange="this.form.submit()" class="form-select form-select-sm" style="width: auto;">
                                                    <option value="live" <?php echo $match['status'] == 'live' ? 'selected' : ''; ?>>Live</option>
                                                    <option value="ended" <?php echo $match['status'] == 'ended' ? 'selected' : ''; ?>>Ended</option>
                                                </select>
                                                <input type="hidden" name="update_status" value="1">
                                            </form>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Device Distribution Chart -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Device Distribution</h5>
                                <canvas id="deviceChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    // Device Chart
    const deviceCtx = document.getElementById('deviceChart').getContext('2d');
    new Chart(deviceCtx, {
        type: 'pie',
        data: {
            labels: <?php echo json_encode(array_column($devices, 'device_type')); ?>,
            datasets: [{
                data: <?php echo json_encode(array_column($devices, 'count')); ?>,
                backgroundColor: [
                    'rgba(255, 99, 132, 0.8)',
                    'rgba(54, 162, 235, 0.8)',
                    'rgba(255, 206, 86, 0.8)'
                ]
            }]
        }
    });

    // Detectează dacă e iOS sau Android și arată instrucțiunile corespunzătoare
    if (/iPhone|iPad|iPod/i.test(navigator.userAgent)) {
        document.querySelector('.android-instructions').style.display = 'none';
        document.querySelector('.ios-instructions').style.display = 'block';
    }
    </script>
</body>
</html>
