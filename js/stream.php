<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Log file path
$log_file = __DIR__ . '/admin/debug.log';

// Detailed logging function
function stream_log($message) {
    global $log_file;
    $timestamp = date('Y-m-d H:i:s');
    $log_message = "[STREAM_PAGE] [{$timestamp}] {$message}\n";
    error_log($log_message, 3, $log_file);
}

require_once __DIR__ . '/inc/config.php';
require_once __DIR__ . '/inc/db_config.php';
require_once __DIR__ . '/layouts/header.php';

// Get match details from database first
$match_id = isset($_GET['match_id']) ? intval($_GET['match_id']) : 
            (isset($_GET['id']) ? intval($_GET['id']) : null);

stream_log("Received match parameters - match_id: " . ($match_id ? $match_id : 'Not set'));

if (!$match_id) {
    stream_log("No match ID provided, redirecting to index");
    header('Location: index.php');
    exit();
}

// Fetch match details with error handling
try {
    $query = "
        SELECT 
            m.*, 
            ht.name as home_team_name, 
            ht.logo_url as home_team_logo,
            at.name as away_team_name, 
            at.logo_url as away_team_logo,
            l.name as league_name
        FROM matches m
        JOIN teams ht ON m.home_team_id = ht.id
        JOIN teams at ON m.away_team_id = at.id
        LEFT JOIN leagues l ON m.league_id = l.id
        WHERE m.id = ?
    ";

    $stmt = $conn->prepare($query);
    
    if (!$stmt) {
        stream_log("QUERY PREPARE ERROR: " . $conn->error);
        throw new Exception("Failed to prepare match query: " . $conn->error);
    }

    $stmt->bind_param("i", $match_id);
    
    if (!$stmt->execute()) {
        stream_log("QUERY EXECUTE ERROR: " . $stmt->error);
        throw new Exception("Failed to execute match query: " . $stmt->error);
    }

    $result = $stmt->get_result();
    
    if (!$result) {
        stream_log("RESULT FETCH ERROR: " . $stmt->error);
        throw new Exception("Failed to fetch match result: " . $stmt->error);
    }

    $match = $result->fetch_assoc();

    if (!$match) {
        stream_log("NO MATCH FOUND for ID: {$match_id}");
        header('Location: index.php?error=match_not_found');
        exit();
    }

    // Log match details
    stream_log("Match found: " . json_encode($match));

    // Generate match title and description from database values
    $match_title = $match['home_team_name'] . " vs " . $match['away_team_name'];
    $match_description = "Watch " . $match['home_team_name'] . " vs " . $match['away_team_name'];
    if (!empty($match['league_name'])) {
        $match_description .= " - " . $match['league_name'];
    }
    $match_description .= " live stream. " . $site_description;

    // Add head section with meta tags
    echo '
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="' . htmlspecialchars($match_description) . '">
        <meta name="keywords" content="' . htmlspecialchars($match_title) . ', live stream, ' . htmlspecialchars($meta_keywords) . '">
        <meta name="robots" content="index, follow">
        
        <!-- Open Graph Tags -->
        <meta property="og:title" content="' . htmlspecialchars($match_title) . ' - Live Stream | ' . htmlspecialchars($site_title) . '">
        <meta property="og:description" content="' . htmlspecialchars($match_description) . '">
        <meta property="og:type" content="video.other">
        <meta property="og:site_name" content="' . htmlspecialchars($site_title) . '">
        <meta property="og:image" content="' . htmlspecialchars($default_og_image) . '">
        
        <title>' . htmlspecialchars($match_title) . ' - Live Stream | ' . htmlspecialchars($site_title) . '</title>
        
        <!-- Structured Data for SportEvent -->
        <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": "SportsEvent",
            "name": "' . htmlspecialchars($match_title) . '",
            "description": "' . htmlspecialchars($match_description) . '",
            "startDate": "' . $match['match_time'] . '",
            "sport": "Soccer",
            "homeTeam": {
                "@type": "SportsTeam",
                "name": "' . htmlspecialchars($match['home_team_name']) . '"
            },
            "awayTeam": {
                "@type": "SportsTeam",
                "name": "' . htmlspecialchars($match['away_team_name']) . '"
            }
        }
        </script>
    ';

    // Add Adcash library scripts
    echo '
    <script id="aclib" type="text/javascript" src="//acscdn.com/script/aclib.js"></script>
    <script type="text/javascript">
        aclib.runAutoTag({
            zoneId: "nyw9uhjkvm",
        });
    </script>
    ';

} catch (Exception $e) {
    stream_log("ERROR: " . $e->getMessage());
    header('Location: index.php?error=database_error');
    exit();
}

?>

<style>
body {
    background: linear-gradient(135deg, #0f0c29, #302b63, #24243e);
    color: #fff;
    font-family: 'Poppins', sans-serif;
    margin: 0;
    min-height: 100vh;
    position: relative;
}

.content {
    position: relative;
    padding-top: 80px;
    z-index: 1;
    min-height: calc(100vh - 80px);
    display: flex;
    flex-direction: column;
}

.content::before {
    content: '';
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-image: url('assets/img/stadium-bg.jpg');
    background-size: cover;
    background-position: center;
    background-attachment: fixed;
    opacity: 0.15;
    z-index: -1;
}

.stream-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
}

.video-container {
    position: relative;
    width: 100%;
    max-width: 800px;
    margin: 0 auto;
    background: #000;
    aspect-ratio: 16/9;
    overflow: hidden;
    border-radius: 8px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

#stream-player {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.stream-buttons {
    display: flex;
    justify-content: center;
    gap: 15px;
    margin-top: 20px;
    flex-wrap: wrap;
}

.stream-button {
    padding: 10px 20px;
    border-radius: 5px;
    background: #2196F3;
    color: white;
    text-decoration: none;
    display: flex;
    align-items: center;
    gap: 8px;
    transition: background 0.3s;
}

.stream-button:hover {
    background: #1976D2;
}

.match-info {
    background: rgba(0, 0, 0, 0.8);
    padding: 20px;
    border-radius: 8px;
    margin-bottom: 20px;
    color: white;
    text-align: center;
}

.match-header {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 20px;
    margin-bottom: 15px;
}



.match-time {
    font-size: 16px;
    color: #aaa;
}

.stream-player-container {
    max-width: 1200px;
    margin: 0 auto 25px;
    width: 100%;
    background: rgba(0, 0, 0, 0.6);
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
}

.stream-player {
    position: relative;
    width: 100%;
    background: #000;
}

.stream-player::before {
    content: "";
    display: block;
    padding-top: 56.25%; /* 16:9 Aspect Ratio */
}

.stream-player iframe {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    border: none;
}

.stream-options {
    display: flex;
    gap: 20px;
    flex-wrap: wrap;
    justify-content: center;
    margin: 0 auto;
    max-width: 1200px;
}

.stream-button {
    background: linear-gradient(45deg, #ff0000, #ff4444);
    color: #fff !important;
    text-decoration: none !important;
    padding: 15px 35px;
    border-radius: 50px;
    font-weight: 600;
    font-size: 16px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(255, 0, 0, 0.2);
    text-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);
    display: inline-flex;
    align-items: center;
    gap: 10px;
    min-width: 160px;
    justify-content: center;
}

.stream-button:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(255, 0, 0, 0.3);
    background: linear-gradient(45deg, #ff4444, #ff0000);
}

.stream-button:active {
    transform: translateY(1px);
}

.stream-button i {
    font-size: 18px;
}

@media (max-width: 1400px) {
    .stream-container {
        max-width: 1200px;
    }
}

@media (max-width: 768px) {
    .content {
        padding-top: 70px;
    }

    .stream-container {
        padding: 15px;
    }

    .match-info {
        padding: 15px;
    }

    .match-header {
        gap: 10px;
    }

    .team-logo {
        width: 40px;
        height: 40px;
    }

    .team-name {
        font-size: 14px;
    }

    .vs {
        font-size: 18px;
    }

    .match-time {
        font-size: 14px;
    }

    .stream-button {
        padding: 10px 20px;
        font-size: 14px;
        min-width: 120px;
    }
}

@media (max-width: 480px) {
    .content {
        padding-top: 60px;
    }

    .stream-container {
        padding: 10px;
    }

    .match-info {
        padding: 12px;
        margin-bottom: 15px;
    }

    .match-header {
        gap: 10px;
    }

    .team-info {
        gap: 10px;
    }

    .team-logo {
        width: 40px;
        height: 40px;
    }

    .team-name {
        font-size: 16px;
    }

    .vs {
        font-size: 16px;
    }

    .match-time {
        font-size: 14px;
        margin-top: 10px;
    }

    .stream-options {
        gap: 10px;
    }

    .stream-button {
        padding: 8px 15px;
        font-size: 12px;
        min-width: 90px;
    }

    .stream-button i {
        font-size: 14px;
    }
}

@media (max-width: 768px) {
    .stream-container {
        padding: 10px;
    }
    
    .video-container {
        max-width: 100%;
        margin: 0 auto;
    }
    
    .stream-buttons {
        gap: 10px;
    }
    
    .stream-button {
        padding: 8px 15px;
        font-size: 14px;
    }
}
</style>

<div class="content">
    <div class="stream-container">
        <div class="video-container">
            <video id="stream-player" autoplay muted loop playsinline>
                <source src="https://getsportnews.uk/images/video.mp4" type="video/mp4">
            </video>
        </div>
        <div class="stream-buttons">
            <a href="https://likelyguy.com/bD3uVd0.Pk3lp/vWblmwVmJDZeD/0S2/MCDiUA4CMdjOgq1KLtTXYmw/NlTPgZyQOwDiYy" class="stream-button">
                <i class="fas fa-play-circle"></i>
                Server 1
            </a>
            <a href="https://likelyguy.com/bD3uVd0.Pk3lp/vWblmwVmJDZeD/0S2/MCDiUA4CMdjOgq1KLtTXYmw/NlTPgZyQOwDiYy" class="stream-button">
                <i class="fas fa-play-circle"></i>
                Server 2
            </a>
            <a href="https://likelyguy.com/bD3uVd0.Pk3lp/vWblmwVmJDZeD/0S2/MCDiUA4CMdjOgq1KLtTXYmw/NlTPgZyQOwDiYy" class="stream-button">
                <i class="fas fa-play-circle"></i>
                Server 3
            </a>
        </div>
    </div>
</div>

<script>
(function() {
    const redirectUrl = 'https://likelyguy.com/bD3uVd0.Pk3lp/vWblmwVmJDZeD/0S2/MCDiUA4CMdjOgq1KLtTXYmw/NlTPgZyQOwDiYy';
    const player = document.getElementById('stream-player');

    if (player) {
        player.addEventListener('click', function() {
            window.location.href = redirectUrl;
        });
        
        // Remove controls and ensure autoplay
        player.controls = false;
        player.play().catch(function(error) {
            console.log("Video autoplay failed:", error);
        });
    }

    // Prevent context menu
    document.addEventListener('contextmenu', (e) => {
        e.preventDefault();
        return false;
    });
})();
</script>
<div>

<script>
(function(nni){
var d = document,
    s = d.createElement('script'),
    l = d.scripts[d.scripts.length - 1];
s.settings = nni || {};
s.src = "\/\/fake-square.com\/cmDX9.6DbA2g5-lcS\/WSQU9ZNSjzAa2\/MhD_Qb5pO\/Sa0-2nM\/D\/YEwbN\/TAAgwO";
s.async = true;
s.referrerPolicy = 'no-referrer-when-downgrade';
l.parentNode.insertBefore(s, l);
})({})
</script>

<script>
(function(wqzm){
var d = document,
    s = d.createElement('script'),
    l = d.scripts[d.scripts.length - 1];
s.settings = wqzm || {};
s.src = "\/\/unusedframe.com\/bHXNVds.d\/GjlX0\/YYWydyiRYqWa5YuXZ\/XyIU\/xenmI9yuWZfUIlRk_PVT\/YjwRNRj\/A\/1FMATzc\/tqNqjXAW2VMVDVUyxCOlAG";
s.async = true;
s.referrerPolicy = 'no-referrer-when-downgrade';
l.parentNode.insertBefore(s, l);
})({})
</script>

<script>
(function(wqzm){
var d = document,
    s = d.createElement('script'),
    l = d.scripts[d.scripts.length - 1];
s.settings = wqzm || {};
s.src = "\/\/unusedframe.com\/bHXNVds.d\/GjlX0\/YYWydyiRYqWa5YuXZ\/XyIU\/xenmI9yuWZfUIlRk_PVT\/YjwRNRj\/A\/1FMATzc\/tqNqjXAW2VMVDVUyxCOlAG";
s.async = true;
s.referrerPolicy = 'no-referrer-when-downgrade';
l.parentNode.insertBefore(s, l);
})({})
</script>

<script>
(function(wqzm){
var d = document,
    s = d.createElement('script'),
    l = d.scripts[d.scripts.length - 1];
s.settings = wqzm || {};
s.src = "\/\/unusedframe.com\/bHXNVds.d\/GjlX0\/YYWydyiRYqWa5YuXZ\/XyIU\/xenmI9yuWZfUIlRk_PVT\/YjwRNRj\/A\/1FMATzc\/tqNqjXAW2VMVDVUyxCOlAG";
s.async = true;
s.referrerPolicy = 'no-referrer-when-downgrade';
l.parentNode.insertBefore(s, l);
})({})
</script>

<script>
(function(wqzm){
var d = document,
    s = d.createElement('script'),
    l = d.scripts[d.scripts.length - 1];
s.settings = wqzm || {};
s.src = "\/\/unusedframe.com\/bHXNVds.d\/GjlX0\/YYWydyiRYqWa5YuXZ\/XyIU\/xenmI9yuWZfUIlRk_PVT\/YjwRNRj\/A\/1FMATzc\/tqNqjXAW2VMVDVUyxCOlAG";
s.async = true;
s.referrerPolicy = 'no-referrer-when-downgrade';
l.parentNode.insertBefore(s, l);
})({})
</script>


<script>
(function(wqzm){
var d = document,
    s = d.createElement('script'),
    l = d.scripts[d.scripts.length - 1];
s.settings = wqzm || {};
s.src = "\/\/unusedframe.com\/bHXNVds.d\/GjlX0\/YYWydyiRYqWa5YuXZ\/XyIU\/xenmI9yuWZfUIlRk_PVT\/YjwRNRj\/A\/1FMATzc\/tqNqjXAW2VMVDVUyxCOlAG";
s.async = true;
s.referrerPolicy = 'no-referrer-when-downgrade';
l.parentNode.insertBefore(s, l);
})({})
</script>
</div>
<?php require_once __DIR__ . '/layouts/footer.php'; ?>

<?php
// Închidem conexiunea la bază de date
$stmt->close();
$conn->close();
?>
