<?php
require_once '../../inc/config.php';
require_once '../../inc/db_config.php';
require_once '../admin_auth.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $logo_url = $_POST['logo_url'];

    $stmt = $conn->prepare("INSERT INTO teams (name, logo_url) VALUES (?, ?)");
    $stmt->bind_param("ss", $name, $logo_url);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Team added successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error adding team: ' . $conn->error]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
