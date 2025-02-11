<?php
require_once '../inc/config.php';
require_once '../inc/db_config.php';
require_once 'admin_auth.php';

// Get leagues
$leagues_query = "SELECT id, name FROM leagues ORDER BY name ASC";
$leagues_result = $conn->query($leagues_query);
$leagues = [];
while ($row = $leagues_result->fetch_assoc()) {
    $leagues[] = $row;
}

// Get teams
$teams_query = "SELECT id, name FROM teams ORDER BY name ASC";
$teams_result = $conn->query($teams_query);
$teams = [];
while ($row = $teams_result->fetch_assoc()) {
    $teams[] = $row;
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $league_id = $_POST['league_id'];
    $home_team_id = $_POST['home_team_id'];
    $away_team_id = $_POST['away_team_id'];
    $match_date = $_POST['match_date'];
    $match_time = $_POST['match_time'];
    $stream_url = $_POST['stream_url'];
    
    // Combine date and time
    $match_datetime = date('Y-m-d H:i:s', strtotime("$match_date $match_time"));
    
    // Generate slug
    $home_team_name = '';
    $away_team_name = '';
    foreach ($teams as $team) {
        if ($team['id'] == $home_team_id) $home_team_name = $team['name'];
        if ($team['id'] == $away_team_id) $away_team_name = $team['name'];
    }
    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $home_team_name . '-vs-' . $away_team_name)));
    
    // Insert into database
    $stmt = $conn->prepare("INSERT INTO matches (league_id, home_team_id, away_team_id, match_time, sport, slug, stream_url) VALUES (?, ?, ?, ?, 'football', ?, ?)");
    $stmt->bind_param("iiisss", $league_id, $home_team_id, $away_team_id, $match_datetime, $slug, $stream_url);
    
    if ($stmt->execute()) {
        header('Location: ' . get_url('admin/matches.php'));
        exit;
    } else {
        $error = "Error adding match: " . $conn->error;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Add Match</title>
    <link rel="stylesheet" href="<?php echo get_url('admin/css/style.css'); ?>">
</head>
<body>
    <div class="container">
        <h1>Add New Match</h1>
        <?php if (isset($error)): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="form-group">
                <label>League:</label>
                <select name="league_id" required>
                    <option value="">Select League</option>
                    <?php foreach ($leagues as $league): ?>
                        <option value="<?php echo $league['id']; ?>"><?php echo htmlspecialchars($league['name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label>Home Team:</label>
                <select name="home_team_id" required>
                    <option value="">Select Home Team</option>
                    <?php foreach ($teams as $team): ?>
                        <option value="<?php echo $team['id']; ?>"><?php echo htmlspecialchars($team['name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label>Away Team:</label>
                <select name="away_team_id" required>
                    <option value="">Select Away Team</option>
                    <?php foreach ($teams as $team): ?>
                        <option value="<?php echo $team['id']; ?>"><?php echo htmlspecialchars($team['name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label>Match Date:</label>
                <input type="date" name="match_date" required>
            </div>
            
            <div class="form-group">
                <label>Match Time:</label>
                <input type="time" name="match_time" required>
            </div>
            
            <div class="form-group">
                <label>Stream URL:</label>
                <input type="url" name="stream_url">
            </div>
            
            <div class="form-group">
                <button type="submit">Add Match</button>
                <a href="<?php echo get_url('admin/matches.php'); ?>" class="button">Cancel</a>
            </div>
        </form>
    </div>
</body>
</html>