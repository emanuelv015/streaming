<?php
session_start();
require_once '../db_connect.php';

// Check if user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $home_team_id = $_POST['home_team'];
    $away_team_id = $_POST['away_team'];
    $match_time = $_POST['match_time'];
    $stream_link = $_POST['stream_link'];
    $sport = $_POST['sport'];

    // Prepare and execute SQL to insert match
    $sql = "INSERT INTO matches (home_team_id, away_team_id, match_time, stream_link, sport) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iisss", $home_team_id, $away_team_id, $match_time, $stream_link, $sport);
    
    if ($stmt->execute()) {
        $_SESSION['message'] = "Match added successfully!";
    } else {
        $_SESSION['error'] = "Error adding match: " . $stmt->error;
    }
    
    $stmt->close();
    header("Location: add_event.php");
    exit();
}

// Fetch all teams for dropdowns
$teams_query = "SELECT id, name FROM teams ORDER BY name";
$teams_result = $conn->query($teams_query);
$teams = [];
while ($row = $teams_result->fetch_assoc()) {
    $teams[] = $row;
}
$teams_json = json_encode($teams);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Match</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        body { font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; }
        select, input { width: 100%; padding: 8px; margin-bottom: 10px; }
        input[type="submit"] { background-color: #4CAF50; color: white; border: none; cursor: pointer; }
        input[type="submit"]:hover { background-color: #45a049; }
    </style>
</head>
<body>
    <h2>Add New Match</h2>
    
    <?php 
    if (isset($_SESSION['message'])) {
        echo "<p style='color:green;'>" . $_SESSION['message'] . "</p>";
        unset($_SESSION['message']);
    }
    if (isset($_SESSION['error'])) {
        echo "<p style='color:red;'>" . $_SESSION['error'] . "</p>";
        unset($_SESSION['error']);
    }
    ?>
    
    <form method="POST" action="">
        <div class="form-group">
            <label>Home Team:</label>
            <select name="home_team" id="home_team" required>
                <option value="">Select Home Team</option>
            </select>
        </div>
        
        <div class="form-group">
            <label>Away Team:</label>
            <select name="away_team" id="away_team" required>
                <option value="">Select Away Team</option>
            </select>
        </div>
        
        <div class="form-group">
            <label>Match Time:</label>
            <input type="datetime-local" name="match_time" required>
        </div>
        
        <div class="form-group">
            <label>Stream Link:</label>
            <input type="url" name="stream_link" placeholder="Optional stream link">
        </div>
        
        <div class="form-group">
            <label>Sport:</label>
            <select name="sport" required>
                <option value="football">Football</option>
                <option value="basketball">Basketball</option>
                <option value="other">Other</option>
            </select>
        </div>
        
        <input type="submit" value="Add Match">
    </form>

    <script>
        const teams = <?php echo $teams_json; ?>;
        const homeTeamSelect = document.getElementById('home_team');
        const awayTeamSelect = document.getElementById('away_team');

        function populateTeamSelect(select, teams) {
            select.innerHTML = '<option value="">Select Team</option>';
            teams.forEach(team => {
                const option = document.createElement('option');
                option.value = team.id;
                option.textContent = team.name;
                select.appendChild(option);
            });
        }

        // Populate team selects
        populateTeamSelect(homeTeamSelect, teams);
        populateTeamSelect(awayTeamSelect, teams);
    </script>
</body>
</html>
