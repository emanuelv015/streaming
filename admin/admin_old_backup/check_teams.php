<?php
include '../inc/conf.php';
include '../inc/db_config.php';
require_once 'admin_auth.php';
check_admin_access(); 

$sql = "SELECT * FROM teams ORDER BY name ASC";
$result = $conn->query($sql);

if ($result) {
    echo '<h1>Echipe disponibile:</h1>';
    echo '<ul>';
    while ($team = $result->fetch_assoc()) {
        echo '<li>' . htmlspecialchars($team['name']) . ' (ID: ' . $team['id'] . ')</li>';
    }
    echo '</ul>';
} else {
    echo 'Eroare la preluarea echipelor: ' . $conn->error;
}
?>
