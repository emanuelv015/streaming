<?php
require_once '../../inc/config.php';
require_once '../../inc/db_config.php';
require_once '../admin_auth.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];

    $stmt = $conn->prepare("DELETE FROM teams WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Team deleted successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error deleting team: ' . $conn->error]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
