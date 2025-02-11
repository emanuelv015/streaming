<?php
require_once '../inc/config.php';

header('Content-Type: application/json');

// Get POST data
$data = json_decode(file_get_contents('php://input'), true);

if (isset($data['flag'])) {
    $flag_file = '../assets/img/flags/' . basename($data['flag']);
    
    // Check if file exists and is within the flags directory
    if (file_exists($flag_file) && strpos(realpath($flag_file), realpath('../assets/img/flags/')) === 0) {
        if (unlink($flag_file)) {
            echo json_encode(['success' => true]);
            exit;
        }
    }
}

echo json_encode(['success' => false]);
?>
