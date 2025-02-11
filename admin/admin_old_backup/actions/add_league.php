<?php
require_once '../../inc/config.php';
require_once '../../inc/db_config.php';
require_once '../admin_auth.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $flag_url = $_POST['flag_url'];

    $stmt = $conn->prepare("INSERT INTO leagues (name, flag_url) VALUES (?, ?)");
    $stmt->bind_param("ss", $name, $flag_url);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'League added successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error adding league: ' . $conn->error]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
