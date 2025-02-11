<?php
require_once '../inc/db_config.php';
require_once 'admin_auth.php';
check_admin_access(); 

header('Content-Type: application/json');

function writeLog($message) {
    $logFile = __DIR__ . '/debug.log';
    $timestamp = date('Y-m-d H:i:s');
    file_put_contents($logFile, "[$timestamp] $message\n", FILE_APPEND);
}

try {
    // Verificăm dacă avem un ID valid
    if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
        throw new Exception("ID invalid");
    }

    $id = (int)$_GET['id'];
    writeLog("Attempting to delete event with ID: " . $id);

    // Pregătim și executăm query-ul de ștergere
    $stmt = $conn->prepare("DELETE FROM matches WHERE id = ?");
    if (!$stmt) {
        throw new Exception("Eroare la pregătirea query-ului: " . $conn->error);
    }

    $stmt->bind_param("i", $id);
    
    if (!$stmt->execute()) {
        throw new Exception("Eroare la ștergerea evenimentului: " . $stmt->error);
    }

    if ($stmt->affected_rows === 0) {
        throw new Exception("Nu s-a găsit niciun eveniment cu ID-ul specificat");
    }

    writeLog("Event deleted successfully");
    
    // Redirecționăm înapoi la pagina principală
    header("Location: index.php?success=1");
    exit();

} catch (Exception $e) {
    writeLog("Error: " . $e->getMessage());
    header("Location: index.php?error=" . urlencode($e->getMessage()));
    exit();
}
