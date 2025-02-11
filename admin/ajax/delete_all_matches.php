<?php
require_once '../inc/config.php';
require_once '../inc/db_config.php';
require_once '../inc/auth.php';

// Verifică dacă utilizatorul este admin
if (!isAdmin()) {
    die(json_encode(['success' => false, 'message' => 'Unauthorized access']));
}

try {
    // Șterge efectiv meciurile terminate din baza de date
    $query = "DELETE FROM matches WHERE status = 'finished'";
    $result = $conn->query($query);

    if ($result) {
        $rowsDeleted = $conn->affected_rows;
        echo json_encode([
            'success' => true, 
            'message' => "Successfully deleted $rowsDeleted matches"
        ]);
    } else {
        echo json_encode([
            'success' => false, 
            'message' => 'Database error: ' . $conn->error
        ]);
    }
} catch (Exception $e) {
    echo json_encode([
        'success' => false, 
        'message' => 'Error: ' . $e->getMessage()
    ]);
} 