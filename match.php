<?php
require_once 'inc/tracking.php';
require_once 'inc/config.php';
require_once 'inc/db_config.php';

// Get match slug from URL
$slug = isset($_GET['slug']) ? $_GET['slug'] : '';

if (empty($slug)) {
    header("Location: index.php");
    exit();
}

// Get match details
$query = "SELECT * FROM matches WHERE slug = ?";

$stmt = $conn->prepare($query);
if ($stmt === false) {
    die("Error preparing statement: " . $conn->error);
}

$stmt->bind_param("s", $slug);
$stmt->execute();
$result = $stmt->get_result();
$match = $result->fetch_assoc();

if (!$match) {
    header("Location: index.php");
    exit();
}

// Get team names from IDs
$home_team_query = "SELECT * FROM teams WHERE id = ?";
$away_team_query = "SELECT * FROM teams WHERE id = ?";

$home_team = null;
$away_team = null;

if ($stmt = $conn->prepare($home_team_query)) {
    $stmt->bind_param("i", $match['home_team_id']);
    $stmt->execute();
    $home_team = $stmt->get_result()->fetch_assoc();
}

if ($stmt = $conn->prepare($away_team_query)) {
    $stmt->bind_param("i", $match['away_team_id']);
    $stmt->execute();
    $away_team = $stmt->get_result()->fetch_assoc();
}

// Format match title using team IDs if team names are not available
$home_team_name = $home_team ? $home_team['name'] : 'Team ' . $match['home_team_id'];
$away_team_name = $away_team ? $away_team['name'] : 'Team ' . $match['away_team_id'];
$match_title = $home_team_name . ' vs ' . $away_team_name;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $match_title; ?> - Live Stream - <?php echo $site_title; ?></title>
    
    <!-- Meta tags for SEO -->
    <meta name="description" content="Watch <?php echo $match_title; ?> live stream. High quality stream available for this match.">
    <meta name="keywords" content="<?php echo $home_team_name; ?>, <?php echo $away_team_name; ?>, live stream, watch online">
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="<?php echo $base_url; ?>/images/favicon-32x32.png">
    
    <!-- CSS -->
    <link rel="stylesheet" href="<?php echo $base_url; ?>assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <header>
        <nav>
            <div class="logo">
                <a href="<?php echo $base_url; ?>">
                    <img src="<?php echo $base_url; ?>assets/img/logo.png" alt="<?php echo $site_title; ?>">
                </a>
            </div>
            <ul>
                <li><a href="<?php echo $base_url; ?>">Home</a></li>
                <li><a href="<?php echo $base_url; ?>schedule.php">Schedule</a></li>
                <li><a href="<?php echo $base_url; ?>contact.php">Contact</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <div class="match-container">
            <div class="match-header">
                <h1><?php echo $match_title; ?></h1>
                <div class="match-meta">
                    <span class="league"><?php echo $match['league']; ?></span>
                    <span class="date"><?php echo date('d M Y H:i', strtotime($match['match_time'])); ?></span>
                    <span class="status <?php echo strtolower($match['status']); ?>"><?php echo $match['status']; ?></span>
                </div>
            </div>

            <?php if ($match['stream_url']): ?>
            <div class="stream-container">
                <iframe src="<?php echo $match['stream_url']; ?>" 
                        frameborder="0" 
                        allowfullscreen="true" 
                        scrolling="no" 
                        height="500" 
                        width="100%">
                </iframe>
            </div>
            <?php else: ?>
            <div class="no-stream">
                <p>Stream will be available closer to match time.</p>
            </div>
            <?php endif; ?>

            <div class="match-info">
                <div class="team home">
                    <img src="<?php echo $home_team ? $home_team['logo_url'] : $base_url . 'assets/img/default-team.png'; ?>" 
                         alt="<?php echo $home_team_name; ?>" 
                         class="team-logo">
                    <h2><?php echo $home_team_name; ?></h2>
                </div>
                <div class="vs">VS</div>
                <div class="team away">
                    <img src="<?php echo $away_team ? $away_team['logo_url'] : $base_url . 'assets/img/default-team.png'; ?>" 
                         alt="<?php echo $away_team_name; ?>" 
                         class="team-logo">
                    <h2><?php echo $away_team_name; ?></h2>
                </div>
            </div>
        </div>
    </main>

    <footer>
        <p>&copy; <?php echo date('Y'); ?> <?php echo $site_title; ?>. All rights reserved.</p>
    </footer>

    <!-- JavaScript -->
    <script src="<?php echo $base_url; ?>assets/js/main.js"></script>
</body>
</html>
