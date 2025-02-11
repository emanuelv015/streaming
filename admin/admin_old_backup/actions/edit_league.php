<?php
require_once '../../inc/config.php';
require_once '../../inc/db_config.php';
require_once '../admin_auth.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $flag_url = $_POST['flag_url'];

    $stmt = $conn->prepare("UPDATE leagues SET name = ?, flag_url = ? WHERE id = ?");
    $stmt->bind_param("ssi", $name, $flag_url, $id);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'League updated successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error updating league: ' . $conn->error]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
