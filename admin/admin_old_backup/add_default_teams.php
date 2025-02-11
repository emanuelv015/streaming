<?php
include '../inc/conf.php';
include '../inc/db_config.php';
require_once 'admin_auth.php';
check_admin_access(); 

$default_teams = [
    // România
    ['FCSB', 'https://upload.wikimedia.org/wikipedia/ro/thumb/d/d5/Sigla_FCSB.svg/120px-Sigla_FCSB.svg.png', 'ro'],
    ['CFR Cluj', 'https://upload.wikimedia.org/wikipedia/ro/thumb/b/ba/CFR_Cluj_logo_2019.png/120px-CFR_Cluj_logo_2019.png', 'ro'],
    ['Rapid București', 'https://upload.wikimedia.org/wikipedia/ro/thumb/3/32/FC_Rapid_Bucuresti.svg/120px-FC_Rapid_Bucuresti.svg.png', 'ro'],
    ['Universitatea Craiova', 'https://upload.wikimedia.org/wikipedia/ro/thumb/8/83/CS_Universitatea_Craiova_logo.svg/120px-CS_Universitatea_Craiova_logo.svg.png', 'ro'],
    ['Dinamo București', 'https://upload.wikimedia.org/wikipedia/ro/thumb/0/0a/FC_Dinamo_Bucuresti.svg/120px-FC_Dinamo_Bucuresti.svg.png', 'ro'],
    
    // Anglia
    ['Manchester City', 'https://upload.wikimedia.org/wikipedia/en/thumb/e/eb/Manchester_City_FC_badge.svg/120px-Manchester_City_FC_badge.svg.png', 'gb'],
    ['Liverpool', 'https://upload.wikimedia.org/wikipedia/en/thumb/0/0c/Liverpool_FC.svg/120px-Liverpool_FC.svg.png', 'gb'],
    ['Manchester United', 'https://upload.wikimedia.org/wikipedia/en/thumb/7/7a/Manchester_United_FC_crest.svg/120px-Manchester_United_FC_crest.svg.png', 'gb'],
    ['Arsenal', 'https://upload.wikimedia.org/wikipedia/en/thumb/5/53/Arsenal_FC.svg/120px-Arsenal_FC.svg.png', 'gb'],
    ['Chelsea', 'https://upload.wikimedia.org/wikipedia/en/thumb/c/cc/Chelsea_FC.svg/120px-Chelsea_FC.svg.png', 'gb'],
    
    // Spania
    ['Real Madrid', 'https://upload.wikimedia.org/wikipedia/en/thumb/5/56/Real_Madrid_CF.svg/120px-Real_Madrid_CF.svg.png', 'es'],
    ['Barcelona', 'https://upload.wikimedia.org/wikipedia/en/thumb/4/47/FC_Barcelona_%28crest%29.svg/120px-FC_Barcelona_%28crest%29.svg.png', 'es'],
    ['Atletico Madrid', 'https://upload.wikimedia.org/wikipedia/en/thumb/f/f4/Atletico_Madrid_2017_logo.svg/120px-Atletico_Madrid_2017_logo.svg.png', 'es'],
    
    // Italia
    ['AC Milan', 'https://upload.wikimedia.org/wikipedia/commons/thumb/d/d0/AC_Milan_logo.svg/120px-AC_Milan_logo.svg.png', 'it'],
    ['Inter Milan', 'https://upload.wikimedia.org/wikipedia/commons/thumb/0/05/FC_Internazionale_Milano_2021.svg/120px-FC_Internazionale_Milano_2021.svg.png', 'it'],
    ['Juventus', 'https://upload.wikimedia.org/wikipedia/commons/thumb/b/bc/Juventus_FC_2017_icon.svg/120px-Juventus_FC_2017_icon.svg.png', 'it'],
    
    // Germania
    ['Bayern Munich', 'https://upload.wikimedia.org/wikipedia/commons/thumb/1/1b/FC_Bayern_M%C3%BCnchen_logo_%282017%29.svg/120px-FC_Bayern_M%C3%BCnchen_logo_%282017%29.svg.png', 'de'],
    ['Borussia Dortmund', 'https://upload.wikimedia.org/wikipedia/commons/thumb/6/67/Borussia_Dortmund_logo.svg/120px-Borussia_Dortmund_logo.svg.png', 'de'],
    
    // Franța
    ['Paris Saint-Germain', 'https://upload.wikimedia.org/wikipedia/en/thumb/a/a7/Paris_Saint-Germain_F.C..svg/120px-Paris_Saint-Germain_F.C..svg.png', 'fr'],
];

$stmt = $conn->prepare("INSERT IGNORE INTO teams (name, logo_url, country_code) VALUES (?, ?, ?)");

$added = 0;
foreach ($default_teams as $team) {
    $stmt->bind_param("sss", $team[0], $team[1], $team[2]);
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            $added++;
            echo "Adăugat: " . $team[0] . "<br>";
        }
    } else {
        echo "Eroare la adăugarea " . $team[0] . ": " . $stmt->error . "<br>";
    }
}

echo "<br>Total echipe adăugate: " . $added;
?>
