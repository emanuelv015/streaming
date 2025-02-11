<?php
include '../inc/conf.php';
include '../inc/db_config.php';
require_once 'admin_auth.php';
check_admin_access(); 

$more_teams = [
    // România (Liga 1)
    ['Farul Constanța', 'https://upload.wikimedia.org/wikipedia/ro/thumb/c/c5/FCV_Farul_Constan%C8%9Ba_logo_2021.svg/120px-FCV_Farul_Constan%C8%9Ba_logo_2021.svg.png', 'ro'],
    ['Sepsi OSK', 'https://upload.wikimedia.org/wikipedia/ro/thumb/5/51/Sepsi_OSK_logo.svg/120px-Sepsi_OSK_logo.svg.png', 'ro'],
    ['FC Voluntari', 'https://upload.wikimedia.org/wikipedia/ro/thumb/3/39/FC_Voluntari_logo_2018.png/120px-FC_Voluntari_logo_2018.png', 'ro'],
    ['FC Hermannstadt', 'https://upload.wikimedia.org/wikipedia/ro/thumb/1/14/AFC_Hermannstadt_logo.png/120px-AFC_Hermannstadt_logo.png', 'ro'],
    ['Petrolul Ploiești', 'https://upload.wikimedia.org/wikipedia/ro/thumb/0/0d/FC_Petrolul_Ploie%C8%99ti_logo.svg/120px-FC_Petrolul_Ploie%C8%99ti_logo.svg.png', 'ro'],
    ['FC Botoșani', 'https://upload.wikimedia.org/wikipedia/ro/thumb/2/23/FC_Boto%C8%99ani_logo.svg/120px-FC_Boto%C8%99ani_logo.svg.png', 'ro'],
    ['UTA Arad', 'https://upload.wikimedia.org/wikipedia/ro/thumb/7/7c/UTA_Arad_logo.svg/120px-UTA_Arad_logo.svg.png', 'ro'],
    ['FC U Craiova 1948', 'https://upload.wikimedia.org/wikipedia/ro/thumb/c/c2/FC_U_Craiova_1948_logo.svg/120px-FC_U_Craiova_1948_logo.svg.png', 'ro'],
    ['Oțelul Galați', 'https://upload.wikimedia.org/wikipedia/ro/thumb/0/03/ASC_O%C8%9Belul_Gala%C8%9Bi_logo.svg/120px-ASC_O%C8%9Belul_Gala%C8%9Bi_logo.svg.png', 'ro'],
    ['Poli Iași', 'https://upload.wikimedia.org/wikipedia/ro/thumb/5/5c/ACSM_Politehnica_Ia%C8%99i_logo.svg/120px-ACSM_Politehnica_Ia%C8%99i_logo.svg.png', 'ro'],
    
    // Premier League (restul echipelor)
    ['Tottenham', 'https://upload.wikimedia.org/wikipedia/en/thumb/b/b4/Tottenham_Hotspur.svg/120px-Tottenham_Hotspur.svg.png', 'gb'],
    ['Aston Villa', 'https://upload.wikimedia.org/wikipedia/en/thumb/f/f9/Aston_Villa_FC_crest_%282016%29.svg/120px-Aston_Villa_FC_crest_%282016%29.svg.png', 'gb'],
    ['Brighton', 'https://upload.wikimedia.org/wikipedia/en/thumb/f/fd/Brighton_%26_Hove_Albion_logo.svg/120px-Brighton_%26_Hove_Albion_logo.svg.png', 'gb'],
    ['West Ham', 'https://upload.wikimedia.org/wikipedia/en/thumb/c/c2/West_Ham_United_FC_logo.svg/120px-West_Ham_United_FC_logo.svg.png', 'gb'],
    ['Brentford', 'https://upload.wikimedia.org/wikipedia/en/thumb/2/2a/Brentford_FC_crest.svg/120px-Brentford_FC_crest.svg.png', 'gb'],
    ['Newcastle', 'https://upload.wikimedia.org/wikipedia/en/thumb/5/56/Newcastle_United_Logo.svg/120px-Newcastle_United_Logo.svg.png', 'gb'],
    ['Crystal Palace', 'https://upload.wikimedia.org/wikipedia/en/thumb/0/0c/Crystal_Palace_FC_logo.svg/120px-Crystal_Palace_FC_logo.svg.png', 'gb'],
    ['Fulham', 'https://upload.wikimedia.org/wikipedia/en/thumb/e/eb/Fulham_FC_%28shield%29.svg/120px-Fulham_FC_%28shield%29.svg.png', 'gb'],
    ['Nottingham Forest', 'https://upload.wikimedia.org/wikipedia/en/thumb/e/e5/Nottingham_Forest_F.C._logo.svg/120px-Nottingham_Forest_F.C._logo.svg.png', 'gb'],
    ['Wolves', 'https://upload.wikimedia.org/wikipedia/en/thumb/f/fc/Wolverhampton_Wanderers.svg/120px-Wolverhampton_Wanderers.svg.png', 'gb'],
    ['Bournemouth', 'https://upload.wikimedia.org/wikipedia/en/thumb/e/e5/AFC_Bournemouth_%282013%29.svg/120px-AFC_Bournemouth_%282013%29.svg.png', 'gb'],
    ['Luton Town', 'https://upload.wikimedia.org/wikipedia/en/thumb/8/8b/Luton_Town_F.C._logo.svg/120px-Luton_Town_F.C._logo.svg.png', 'gb'],
    ['Sheffield United', 'https://upload.wikimedia.org/wikipedia/en/thumb/9/9c/Sheffield_United_FC_logo.svg/120px-Sheffield_United_FC_logo.svg.png', 'gb'],
    ['Burnley', 'https://upload.wikimedia.org/wikipedia/en/thumb/6/62/Burnley_F.C._Logo.svg/120px-Burnley_F.C._Logo.svg.png', 'gb'],
    
    // Serie A Italia (restul echipelor)
    ['Napoli', 'https://upload.wikimedia.org/wikipedia/commons/thumb/2/28/S.S.C._Napoli_logo.svg/120px-S.S.C._Napoli_logo.svg.png', 'it'],
    ['Lazio', 'https://upload.wikimedia.org/wikipedia/en/thumb/c/ce/S.S._Lazio_badge.svg/120px-S.S._Lazio_badge.svg.png', 'it'],
    ['Roma', 'https://upload.wikimedia.org/wikipedia/en/thumb/f/f7/AS_Roma_logo_%282017%29.svg/120px-AS_Roma_logo_%282017%29.svg.png', 'it'],
    ['Atalanta', 'https://upload.wikimedia.org/wikipedia/en/thumb/6/66/AtalantaBC.svg/120px-AtalantaBC.svg.png', 'it'],
    ['Fiorentina', 'https://upload.wikimedia.org/wikipedia/commons/thumb/7/79/ACF_Fiorentina.svg/120px-ACF_Fiorentina.svg.png', 'it'],
    ['Bologna', 'https://upload.wikimedia.org/wikipedia/en/thumb/5/5b/Bologna_F.C._1909_logo.svg/120px-Bologna_F.C._1909_logo.svg.png', 'it'],
    ['Torino', 'https://upload.wikimedia.org/wikipedia/en/thumb/2/2e/Torino_FC_Logo.svg/120px-Torino_FC_Logo.svg.png', 'it'],
    ['Monza', 'https://upload.wikimedia.org/wikipedia/en/thumb/7/7e/AC_Monza_logo_%282019%29.svg/120px-AC_Monza_logo_%282019%29.svg.png', 'it'],
    ['Udinese', 'https://upload.wikimedia.org/wikipedia/en/thumb/c/ce/Udinese_Calcio_logo.svg/120px-Udinese_Calcio_logo.svg.png', 'it'],
    ['Sassuolo', 'https://upload.wikimedia.org/wikipedia/en/thumb/1/1c/US_Sassuolo_Calcio_logo.svg/120px-US_Sassuolo_Calcio_logo.svg.png', 'it'],
    ['Empoli', 'https://upload.wikimedia.org/wikipedia/en/thumb/c/c4/Empoli_F.C._logo.svg/120px-Empoli_F.C._logo.svg.png', 'it'],
    ['Frosinone', 'https://upload.wikimedia.org/wikipedia/en/thumb/9/9f/Frosinone_Calcio_logo.svg/120px-Frosinone_Calcio_logo.svg.png', 'it'],
    ['Genoa', 'https://upload.wikimedia.org/wikipedia/en/thumb/4/46/Genoa_C.F.C._logo.svg/120px-Genoa_C.F.C._logo.svg.png', 'it'],
    ['Verona', 'https://upload.wikimedia.org/wikipedia/en/thumb/9/92/Hellas_Verona_FC_logo_%282020%29.svg/120px-Hellas_Verona_FC_logo_%282020%29.svg.png', 'it'],
    ['Cagliari', 'https://upload.wikimedia.org/wikipedia/en/thumb/6/61/Cagliari_Calcio_1920.svg/120px-Cagliari_Calcio_1920.svg.png', 'it'],
    ['Salernitana', 'https://upload.wikimedia.org/wikipedia/en/thumb/4/4f/U.S._Salernitana_1919_logo.svg/120px-U.S._Salernitana_1919_logo.svg.png', 'it'],
    
    // La Liga (restul echipelor)
    ['Girona', 'https://upload.wikimedia.org/wikipedia/en/thumb/d/d3/Girona_FC_new_logo.svg/120px-Girona_FC_new_logo.svg.png', 'es'],
    ['Athletic Bilbao', 'https://upload.wikimedia.org/wikipedia/en/thumb/9/98/Athletic_Club_Bilbao_logo.svg/120px-Athletic_Club_Bilbao_logo.svg.png', 'es'],
    ['Real Sociedad', 'https://upload.wikimedia.org/wikipedia/en/thumb/f/f1/Real_Sociedad_logo.svg/120px-Real_Sociedad_logo.svg.png', 'es'],
    ['Real Betis', 'https://upload.wikimedia.org/wikipedia/en/thumb/1/13/Real_betis_logo.svg/120px-Real_betis_logo.svg.png', 'es'],
    ['Valencia', 'https://upload.wikimedia.org/wikipedia/en/thumb/c/ce/Valenciacf.svg/120px-Valenciacf.svg.png', 'es'],
    ['Las Palmas', 'https://upload.wikimedia.org/wikipedia/en/thumb/5/cinquantacinque/UD_Las_Palmas_logo.svg/120px-UD_Las_Palmas_logo.svg.png', 'es'],
    ['Rayo Vallecano', 'https://upload.wikimedia.org/wikipedia/en/thumb/1/17/Rayo_Vallecano_logo.png/120px-Rayo_Vallecano_logo.png', 'es'],
    ['Osasuna', 'https://upload.wikimedia.org/wikipedia/en/thumb/c/c3/CA_Osasuna_logo.svg/120px-CA_Osasuna_logo.svg.png', 'es'],
    ['Villarreal', 'https://upload.wikimedia.org/wikipedia/en/thumb/7/70/Villarreal_CF_logo.svg/120px-Villarreal_CF_logo.svg.png', 'es'],
    ['Getafe', 'https://upload.wikimedia.org/wikipedia/en/thumb/4/46/Getafe_CF_logo.svg/120px-Getafe_CF_logo.svg.png', 'es'],
    ['Mallorca', 'https://upload.wikimedia.org/wikipedia/en/thumb/e/e0/Rcd_mallorca.svg/120px-Rcd_mallorca.svg.png', 'es'],
    ['Sevilla', 'https://upload.wikimedia.org/wikipedia/en/thumb/3/3b/Sevilla_FC_logo.svg/120px-Sevilla_FC_logo.svg.png', 'es'],
    ['Alaves', 'https://upload.wikimedia.org/wikipedia/en/thumb/2/2e/Deportivo_Alav%C3%A9s_logo.svg/120px-Deportivo_Alav%C3%A9s_logo.svg.png', 'es'],
    ['Celta Vigo', 'https://upload.wikimedia.org/wikipedia/en/thumb/1/12/RC_Celta_de_Vigo_logo.svg/120px-RC_Celta_de_Vigo_logo.svg.png', 'es'],
    ['Granada', 'https://upload.wikimedia.org/wikipedia/en/thumb/5/5c/Granada_CF_logo.svg/120px-Granada_CF_logo.svg.png', 'es'],
    ['Cadiz', 'https://upload.wikimedia.org/wikipedia/en/thumb/5/58/C%C3%A1diz_CF_logo.svg/120px-C%C3%A1diz_CF_logo.svg.png', 'es'],
    ['Almeria', 'https://upload.wikimedia.org/wikipedia/en/thumb/2/23/UD_Almer%C3%ADa_logo.svg/120px-UD_Almer%C3%ADa_logo.svg.png', 'es']
];

$stmt = $conn->prepare("INSERT IGNORE INTO teams (name, logo_url, country_code) VALUES (?, ?, ?)");

$added = 0;
foreach ($more_teams as $team) {
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
