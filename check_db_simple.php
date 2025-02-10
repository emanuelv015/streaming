<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    $conn = new mysqli('localhost', 'root', '', 'streamthunder_events');
    
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $output = "";

    // Get table structure
    $query = "SHOW COLUMNS FROM matches";
    $result = $conn->query($query);

    if ($result) {
        $output .= "Table Structure for 'matches':\n";
        $output .= "===========================\n";
        while ($row = $result->fetch_assoc()) {
            $output .= print_r($row, true) . "\n";
        }
        $output .= "\n";
    } else {
        $output .= "Error getting table structure: " . $conn->error . "\n";
    }

    // Get a sample match to see the data
    $query = "SELECT * FROM matches LIMIT 1";
    $result = $conn->query($query);

    if ($result && $result->num_rows > 0) {
        $output .= "Sample Match Data:\n";
        $output .= "=================\n";
        $output .= print_r($result->fetch_assoc(), true) . "\n";
    } else {
        $output .= "No matches found or error: " . $conn->error . "\n";
    }

    // Write output to file
    file_put_contents('db_structure.txt', $output);
    echo "Database structure has been written to db_structure.txt";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
