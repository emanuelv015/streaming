<?php
// Database Configuration
$db_config = array(
    'hostname' => 'localhost',
    'username' => 'getsport_streame', // era greșit 'getsport_streame'
    'password' => 'YMN8#VUE+bEN',
    'database' => 'getsport_stream'
);

// Create connection
$conn = new mysqli(
    $db_config['hostname'],
    $db_config['username'],
    $db_config['password'],
    $db_config['database']
);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set charset
$conn->set_charset("utf8mb4");

// Function to get database connection
function getDBConnection() {
    global $conn;
    return $conn;
}
?>
