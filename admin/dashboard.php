<?php
require_once 'auth.php';
require_once '../inc/db_config.php';

// Verifică perioada selectată
$period = $_GET['period'] ?? 'today';
$start_date = date('Y-m-d');
$end_date = date('Y-m-d');

switch($period) {
    case 'week':
        $start_date = date('Y-m-d', strtotime('-7 days'));
        break;
    case 'month':
        $start_date = date('Y-m-d', strtotime('-30 days'));
        break;
    case 'year':
        $start_date = date('Y-m-d', strtotime('-365 days'));
        break;
}

// Statistici generale
$stats = [
    'total_visits' => 0,
    'unique_visitors' => 0,
    'avg_time' => 0,
    'popular_matches' => [],
    'top_countries' => [],
    'devices' => [],
    'peak_hours' => []
];

// Total vizite și vizitatori unici
$query = "SELECT 
            COUNT(*) as total_visits,
            COUNT(DISTINCT session_id) as unique_visitors,
            AVG(time_spent) as avg_time
          FROM user_visits 
          WHERE DATE(created_at) BETWEEN ? AND ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ss", $start_date, $end_date);
$stmt->execute();
$result = $stmt->get_result()->fetch_assoc();

$stats['total_visits'] = $result['total_visits'];
$stats['unique_visitors'] = $result['unique_visitors'];
$stats['avg_time'] = round($result['avg_time'] / 60, 2); // convertește în minute

// Meciuri populare
$query = "SELECT 
            m.id,
            m.home_team,
            m.away_team,
            COUNT(*) as views,
            AVG(s.avg_watch_time) as avg_watch_time
          FROM matches m
          JOIN stream_stats s ON m.id = s.match_id
          WHERE DATE(s.created_at) BETWEEN ? AND ?
          GROUP BY m.id
          ORDER BY views DESC
          LIMIT 5";
$stmt = $conn->prepare($query);
$stmt->bind_param("ss", $start_date, $end_date);
$stmt->execute();
$stats['popular_matches'] = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Statistics</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <?php include 'includes/sidebar.php'; ?>

            <!-- Main content -->
            <div class="col-md-10 ms-sm-auto px-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1>Dashboard Statistics</h1>
                    <div class="btn-group">
                        <a href="?period=today" class="btn btn-sm btn-outline-secondary <?php echo $period == 'today' ? 'active' : ''; ?>">Today</a>
                        <a href="?period=week" class="btn btn-sm btn-outline-secondary <?php echo $period == 'week' ? 'active' : ''; ?>">Week</a>
                        <a href="?period=month" class="btn btn-sm btn-outline-secondary <?php echo $period == 'month' ? 'active' : ''; ?>">Month</a>
                        <a href="?period=year" class="btn btn-sm btn-outline-secondary <?php echo $period == 'year' ? 'active' : ''; ?>">Year</a>
                    </div>
                </div>

                <!-- Stats Overview -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Total Visits</h5>
                                <h2><?php echo number_format($stats['total_visits']); ?></h2>
                                <p class="text-muted">In selected period</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Unique Visitors</h5>
                                <h2><?php echo number_format($stats['unique_visitors']); ?></h2>
                                <p class="text-muted">Distinct users</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Avg. Time on Site</h5>
                                <h2><?php echo $stats['avg_time']; ?> min</h2>
                                <p class="text-muted">Per session</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Active Streams</h5>
                                <h2><?php echo count($stats['popular_matches']); ?></h2>
                                <p class="text-muted">Currently live</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Charts Row -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Visitors Over Time</h5>
                                <canvas id="visitorsChart"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Popular Matches</h5>
                                <canvas id="matchesChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Detailed Stats -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Popular Matches Details</h5>
                                <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>Match</th>
                                                <th>Views</th>
                                                <th>Avg. Watch Time</th>
                                                <th>Peak Viewers</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($stats['popular_matches'] as $match): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($match['home_team'] . ' vs ' . $match['away_team']); ?></td>
                                                <td><?php echo number_format($match['views']); ?></td>
                                                <td><?php echo round($match['avg_watch_time'] / 60, 2); ?> min</td>
                                                <td><?php echo number_format($match['peak_viewers']); ?></td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    // Inițializare grafice
    const visitorsCtx = document.getElementById('visitorsChart').getContext('2d');
    const matchesCtx = document.getElementById('matchesChart').getContext('2d');

    // Grafic vizitatori
    new Chart(visitorsCtx, {
        type: 'line',
        data: {
            labels: <?php echo json_encode($visitor_labels); ?>,
            datasets: [{
                label: 'Visitors',
                data: <?php echo json_encode($visitor_data); ?>,
                borderColor: 'rgb(75, 192, 192)',
                tension: 0.1
            }]
        }
    });

    // Grafic meciuri populare
    new Chart(matchesCtx, {
        type: 'bar',
        data: {
            labels: <?php echo json_encode($match_labels); ?>,
            datasets: [{
                label: 'Views',
                data: <?php echo json_encode($match_data); ?>,
                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                borderColor: 'rgb(54, 162, 235)',
                borderWidth: 1
            }]
        }
    });
    </script>
</body>
</html> 