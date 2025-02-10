<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'inc/db_config.php';

function displayTableStructure($conn) {
    // Obținem lista tuturor tabelelor
    $tables_query = "SHOW TABLES";
    $tables_result = mysqli_query($conn, $tables_query);

    echo "<h1>Database Structure</h1>";

    while ($table = mysqli_fetch_array($tables_result)) {
        $table_name = $table[0];
        echo "<h2>Table: $table_name</h2>";
        
        // Afișăm structura fiecărei tabele
        $structure_query = "DESCRIBE $table_name";
        $structure_result = mysqli_query($conn, $structure_query);
        
        echo "<table border='1' cellpadding='5'>";
        echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
        
        while ($column = mysqli_fetch_assoc($structure_result)) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($column['Field']) . "</td>";
            echo "<td>" . htmlspecialchars($column['Type']) . "</td>";
            echo "<td>" . htmlspecialchars($column['Null']) . "</td>";
            echo "<td>" . htmlspecialchars($column['Key']) . "</td>";
            echo "<td>" . htmlspecialchars($column['Default'] ?? 'NULL') . "</td>";
            echo "<td>" . htmlspecialchars($column['Extra']) . "</td>";
            echo "</tr>";
        }
        
        echo "</table><hr>";
    }
}

// Verificăm conexiunea
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Afișăm structura bazei de date
displayTableStructure($conn);

//Închidem conexiunea
mysqli_close($conn);
?>
