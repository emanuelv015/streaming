<?php
require_once 'db_config.php';

function create_tables($conn) {
    // Tabel pentru echipe
    $teams_query = "
    CREATE TABLE IF NOT EXISTS teams (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        country VARCHAR(100),
        logo_url TEXT
    )";
    $conn->query($teams_query);

    // Tabel pentru meciuri
    $matches_query = "
    CREATE TABLE IF NOT EXISTS matches (
        id INT AUTO_INCREMENT PRIMARY KEY,
        home_team_id INT,
        away_team_id INT,
        league VARCHAR(100),
        match_date DATE,
        match_time TIME,
        status ENUM('upcoming', 'live', 'completed') DEFAULT 'upcoming',
        FOREIGN KEY (home_team_id) REFERENCES teams(id),
        FOREIGN KEY (away_team_id) REFERENCES teams(id)
    )";
    $conn->query($matches_query);

    // Tabel pentru sursele de streaming
    $stream_sources_query = "
    CREATE TABLE IF NOT EXISTS stream_sources (
        id INT AUTO_INCREMENT PRIMARY KEY,
        match_id INT,
        language VARCHAR(50),
        source_type ENUM('iframe', 'direct_link', 'embed', 'youtube') DEFAULT 'iframe',
        stream_url TEXT,
        is_active BOOLEAN DEFAULT TRUE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (match_id) REFERENCES matches(id) ON DELETE CASCADE
    )";
    $conn->query($stream_sources_query);
}

function insert_sample_data($conn) {
    // Inserare echipe
    $teams_insert = "
    INSERT IGNORE INTO teams (name, country) VALUES 
    ('Real Madrid', 'Spain'),
    ('Barcelona', 'Spain'),
    ('Manchester United', 'England'),
    ('Liverpool', 'England'),
    ('Bayern Munich', 'Germany'),
    ('Borussia Dortmund', 'Germany')
    ";
    $conn->query($teams_insert);

    // Inserare meciuri
    $matches_insert = "
    INSERT IGNORE INTO matches (home_team_id, away_team_id, league, match_date, match_time, status) VALUES
    (
        (SELECT id FROM teams WHERE name = 'Real Madrid'),
        (SELECT id FROM teams WHERE name = 'Barcelona'),
        'La Liga',
        CURDATE() + INTERVAL 1 DAY,
        '20:00:00',
        'upcoming'
    ),
    (
        (SELECT id FROM teams WHERE name = 'Manchester United'),
        (SELECT id FROM teams WHERE name = 'Liverpool'),
        'Premier League',
        CURDATE() + INTERVAL 2 DAY,
        '18:30:00',
        'upcoming'
    )
    ";
    $conn->query($matches_insert);

    // Inserare surse streaming
    $stream_sources_insert = "
    INSERT IGNORE INTO stream_sources (match_id, language, source_type, stream_url) VALUES
    (
        (SELECT id FROM matches WHERE home_team_id = (SELECT id FROM teams WHERE name = 'Real Madrid') 
         AND away_team_id = (SELECT id FROM teams WHERE name = 'Barcelona')),
        'ro',
        'iframe',
        'https://example.com/stream1'
    ),
    (
        (SELECT id FROM matches WHERE home_team_id = (SELECT id FROM teams WHERE name = 'Real Madrid') 
         AND away_team_id = (SELECT id FROM teams WHERE name = 'Barcelona')),
        'en',
        'youtube',
        'https://www.youtube.com/embed/dummyVideoId1'
    ),
    (
        (SELECT id FROM matches WHERE home_team_id = (SELECT id FROM teams WHERE name = 'Manchester United') 
         AND away_team_id = (SELECT id FROM teams WHERE name = 'Liverpool')),
        'ro',
        'iframe',
        'https://example.com/stream2'
    ),
    (
        (SELECT id FROM matches WHERE home_team_id = (SELECT id FROM teams WHERE name = 'Manchester United') 
         AND away_team_id = (SELECT id FROM teams WHERE name = 'Liverpool')),
        'en',
        'youtube',
        'https://www.youtube.com/embed/dummyVideoId2'
    )
    ";
    $conn->query($stream_sources_insert);
}

function setup_database() {
    global $conn;
    
    if (!$conn) {
        die("Conexiune eșuată la baza de date");
    }

    // Dezactivare verificări cheie străină temporar
    $conn->query("SET FOREIGN_KEY_CHECKS=0");

    create_tables($conn);
    insert_sample_data($conn);

    // Reactivare verificări cheie străină
    $conn->query("SET FOREIGN_KEY_CHECKS=1");

    echo "Baza de date a fost configurată cu succes!";
}

// Rulare setup doar dacă este apelat direct
if (basename($_SERVER['PHP_SELF']) === basename(__FILE__)) {
    setup_database();
}
?>
