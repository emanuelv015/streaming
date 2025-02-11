<?php
include 'db_config.php';

// Creăm tabelul pentru meciuri
$sql = "CREATE TABLE IF NOT EXISTS matches (
    id INT AUTO_INCREMENT PRIMARY KEY,
    league VARCHAR(100) NOT NULL,
    league_flag VARCHAR(2) NOT NULL,
    team1 VARCHAR(100) NOT NULL,
    team2 VARCHAR(100) NOT NULL,
    match_date DATE NOT NULL,
    match_time TIME NOT NULL,
    sport VARCHAR(50) NOT NULL,
    status ENUM('LIVE', 'UPCOMING', 'FINISHED') NOT NULL,
    stream_link VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if ($conn->query($sql) === TRUE) {
    echo "Tabelul matches a fost creat cu succes!";
} else {
    echo "Eroare la crearea tabelului: " . $conn->error;
}

// Adăugăm câteva meciuri de exemplu
$sample_matches = array(
    array(
        'league' => 'Ligue 1',
        'league_flag' => 'fr',
        'team1' => 'AS Monaco',
        'team2' => 'Rennes',
        'match_date' => date('Y-m-d'),
        'match_time' => '20:00:00',
        'sport' => 'football',
        'status' => 'LIVE',
        'stream_link' => '#'
    ),
    array(
        'league' => 'Serie A',
        'league_flag' => 'it',
        'team1' => 'Napoli',
        'team2' => 'Juventus',
        'match_date' => date('Y-m-d'),
        'match_time' => '20:00:00',
        'sport' => 'football',
        'status' => 'LIVE',
        'stream_link' => '#'
    ),
    array(
        'league' => 'Premier League',
        'league_flag' => 'gb',
        'team1' => 'Man City',
        'team2' => 'Chelsea',
        'match_date' => date('Y-m-d'),
        'match_time' => '20:00:00',
        'sport' => 'football',
        'status' => 'LIVE',
        'stream_link' => '#'
    )
);

$stmt = $conn->prepare("INSERT INTO matches (league, league_flag, team1, team2, match_date, match_time, sport, status, stream_link) 
                       VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");

foreach ($sample_matches as $match) {
    $stmt->bind_param("sssssssss", 
        $match['league'],
        $match['league_flag'],
        $match['team1'],
        $match['team2'],
        $match['match_date'],
        $match['match_time'],
        $match['sport'],
        $match['status'],
        $match['stream_link']
    );
    $stmt->execute();
}

echo "Meciurile de exemplu au fost adăugate!";
?>
