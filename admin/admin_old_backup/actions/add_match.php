<?php
require_once '../../inc/config.php';
require_once '../../inc/db_config.php';
require_once '../admin_auth.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $home_team_id = $_POST['home_team_id'];
    $away_team_id = $_POST['away_team_id'];
    $league_id = $_POST['league_id'];
    $match_time = $_POST['match_time'];
    $stream_url = $_POST['stream_url'];

    $stmt = $conn->prepare("INSERT INTO matches (home_team_id, away_team_id, league_id, match_time, stream_url) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("iiiss", $home_team_id, $away_team_id, $league_id, $match_time, $stream_url);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Match added successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error adding match: ' . $conn->error]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
