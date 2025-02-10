<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/inc/db_config.php';

// Verificăm conexiunea
if (!$conn) {
    die("Conexiune eșuată: " . mysqli_connect_error());
}

// Interogăm structura tabelei matches
$query = "DESCRIBE matches";
$result = mysqli_query($conn, $query);

if (!$result) {
    die("Eroare la preluarea structurii tabelei: " . mysqli_error($conn));
}

echo "<html><body>";
echo "<h2>Structura tabelei 'matches':</h2>";
echo "<table border='1'>";
echo "<tr><th>Coloană</th><th>Tip</th><th>Null</th><th>Cheie</th><th>Default</th><th>Extra</th></tr>";

while ($row = mysqli_fetch_assoc($result)) {
    echo "<tr>";
    echo "<td>" . htmlspecialchars($row['Field']) . "</td>";
    echo "<td>" . htmlspecialchars($row['Type']) . "</td>";
    echo "<td>" . htmlspecialchars($row['Null']) . "</td>";
    echo "<td>" . htmlspecialchars($row['Key']) . "</td>";
    echo "<td>" . htmlspecialchars($row['Default'] ?? 'NULL') . "</td>";
    echo "<td>" . htmlspecialchars($row['Extra']) . "</td>";
    echo "</tr>";
}

echo "</table>";

// Afișăm și câteva înregistrări de test
$query_data = "SELECT * FROM matches LIMIT 5";
$result_data = mysqli_query($conn, $query_data);

if ($result_data) {
    echo "<h2>Exemple de înregistrări:</h2>";
    echo "<table border='1'>";
    
    // Afișăm antetul
    $headers = mysqli_fetch_fields($result_data);
    echo "<tr>";
    foreach ($headers as $header) {
        echo "<th>" . htmlspecialchars($header->name) . "</th>";
    }
    echo "</tr>";
    
    // Afișăm datele
    mysqli_data_seek($result_data, 0);
    while ($row = mysqli_fetch_assoc($result_data)) {
        echo "<tr>";
        foreach ($row as $value) {
            echo "<td>" . htmlspecialchars($value) . "</td>";
        }
        echo "</tr>";
    }
    
    echo "</table>";
}

mysqli_close($conn);
?>
</body></html>
