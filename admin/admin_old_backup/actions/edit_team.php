<?php
require_once '../../inc/config.php';
require_once '../../inc/db_config.php';
require_once '../admin_auth.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $logo_url = $_POST['logo_url'];

    $stmt = $conn->prepare("UPDATE teams SET name = ?, logo_url = ? WHERE id = ?");
    $stmt->bind_param("ssi", $name, $logo_url, $id);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Team updated successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error updating team: ' . $conn->error]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
