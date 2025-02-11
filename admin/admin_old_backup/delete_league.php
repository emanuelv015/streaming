<?php
require_once '../inc/config.php';
require_once '../inc/db_config.php';

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

if (isset($data['id'])) {
    $league_id = (int)$data['id'];
    
    // Get league info to delete flag file
    $stmt = $conn->prepare("SELECT flag_url FROM leagues WHERE id = ?");
    $stmt->bind_param('i', $league_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $league = $result->fetch_assoc();
    
    // Set league_id to NULL for all matches using this league
    $stmt = $conn->prepare("UPDATE matches SET league_id = NULL WHERE league_id = ?");
    $stmt->bind_param('i', $league_id);
    $stmt->execute();
    
    // Delete league from database
    $stmt = $conn->prepare("DELETE FROM leagues WHERE id = ?");
    $stmt->bind_param('i', $league_id);
    
    if ($stmt->execute()) {
        // Delete flag file if exists
        if ($league && $league['flag_url']) {
            $flag_path = str_replace($base_url, '../', $league['flag_url']);
            if (file_exists($flag_path)) {
                unlink($flag_path);
            }
        }
        echo json_encode(['success' => true]);
        exit;
    }
}

echo json_encode(['success' => false]);
