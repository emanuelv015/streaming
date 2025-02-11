<?php
session_start();
require_once __DIR__ . '/../inc/config.php';
require_once __DIR__ . '/../inc/db_config.php';

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $match_id = $_POST['match_id'];
    $language = $_POST['language'];
    $source_type = $_POST['source_type'];
    $stream_url = $_POST['stream_url'];
    $is_active = isset($_POST['is_active']) ? 1 : 0;

    $stmt = $conn->prepare("INSERT INTO stream_sources (match_id, language, source_type, stream_url, is_active) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("isssi", $match_id, $language, $source_type, $stream_url, $is_active);

    if ($stmt->execute()) {
        $_SESSION['message'] = "Sursă streaming adăugată cu succes!";
        header('Location: add_stream_sources.php');
    } else {
        $_SESSION['error'] = "Eroare la adăugarea sursei: " . $stmt->error;
        header('Location: add_stream_sources.php');
    }
    $stmt->close();
    $conn->close();
} else {
    header('Location: add_stream_sources.php');
}
?>
