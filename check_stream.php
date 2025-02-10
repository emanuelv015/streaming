<?php
header('Content-Type: application/json');
require_once __DIR__ . '/inc/config.php';
require_once __DIR__ . '/inc/db_config.php';

// Get match slug
$slug = isset($_GET['slug']) ? $_GET['slug'] : null;

if (!$slug) {
    echo json_encode(['error' => 'No slug provided']);
    exit();
}

// Check if match exists and get stream URL
$query = "SELECT stream_url FROM matches WHERE slug = ?";
$stmt = $conn->prepare($query);

if ($stmt === false) {
    echo json_encode(['error' => 'Database error']);
    exit();
}

$stmt->bind_param('s', $slug);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['error' => 'Match not found']);
    exit();
}

$match = $result->fetch_assoc();
echo json_encode(['stream_url' => $match['stream_url']]);

$stmt->close();
$conn->close();
