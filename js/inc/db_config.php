<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Log file path
$log_file = __DIR__ . '/../admin/debug.log';

// Production Database Configuration
$db_config = array(
    'hostname' => 'localhost',
    'username' => 'getsport_streamer',
    'password' => 'parolamea1234',
    'database' => 'getsport_stream'
);

// Log connection attempt
error_log("Attempting to connect to database at " . date('Y-m-d H:i:s') . "\n", 3, $log_file);
error_log("Host: {$db_config['hostname']}\n", 3, $log_file);
error_log("Database: {$db_config['database']}\n", 3, $log_file);

// Create connection
$conn = @mysqli_connect(
    $db_config['hostname'],
    $db_config['username'],
    $db_config['password'],
    $db_config['database']
);

// Check connection and show exact error
if (!$conn) {
    $error = mysqli_connect_error();
    error_log("Connection failed: " . $error . "\n", 3, $log_file);
    error_log("PHP version: " . PHP_VERSION . "\n", 3, $log_file);
    error_log("MySQL client version: " . mysqli_get_client_info() . "\n", 3, $log_file);
    
    // Show detailed error in browser
    echo "<pre>";
    echo "Connection failed!\n";
    echo "Error: " . $error . "\n";
    echo "Please check debug.log for more details.";
    echo "</pre>";
    die();
}

// Log successful connection
error_log("Database connection successful at " . date('Y-m-d H:i:s') . "\n", 3, $log_file);

// Set charset
mysqli_set_charset($conn, "utf8mb4");

// Function to get database connection
function getDBConnection() {
    global $conn;
    return $conn;
}
?>
