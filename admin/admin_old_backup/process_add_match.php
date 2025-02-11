<?php
session_start();
require_once '../inc/config.php';
require_once '../inc/db_config.php';
require_once 'admin_auth.php';

// Verifică autentificarea
requireAdminLogin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $home_team_id = intval($_POST['home_team_id']);
        $away_team_id = intval($_POST['away_team_id']);
        $match_time = $_POST['match_time'];
        $league = $_POST['league'];
        $status = $_POST['status'];
        $stream_url = !empty($_POST['stream_url']) ? $_POST['stream_url'] : null;

        $query = "INSERT INTO matches (home_team_id, away_team_id, match_time, league, status, stream_url) 
                 VALUES (?, ?, ?, ?, ?, ?)";
        
        $stmt = $conn->prepare($query);
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $conn->error);
        }

        $stmt->bind_param('iissss', 
            $home_team_id,
            $away_team_id,
            $match_time,
            $league,
            $status,
            $stream_url
        );

        if ($stmt->execute()) {
            header('Location: matches.php?success=1');
            exit();
        } else {
            throw new Exception("Execute failed: " . $stmt->error);
        }
    } catch (Exception $e) {
        error_log("Error adding match: " . $e->getMessage());
        header('Location: add_match.php?error=' . urlencode($e->getMessage()));
        exit();
    }
} else {
    // Dacă nu e POST, redirecționăm
    header("Location: matches.php");
    exit();
} 