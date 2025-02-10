<?php
require_once 'inc/config.php';
require_once 'inc/db_config.php';
 
// Get match data
$slug = $_GET['slug'] ?? '';
$query = "
    SELECT m.*, 
           h.name as home_team_name, 
           a.name as away_team_name,
           l.name as league_name,
           (SELECT COUNT(*) FROM user_actions WHERE match_id = m.id) as viewers
    FROM matches m
    LEFT JOIN teams h ON m.home_team = h.id 
    LEFT JOIN teams a ON m.away_team = a.id 
    LEFT JOIN leagues l ON m.league = l.id
    WHERE m.slug = ? AND (m.status = 'live' OR m.status = 'upcoming')";

$stmt = $conn->prepare($query);
$stmt->bind_param("s", $slug);
$stmt->execute();
$result = $stmt->get_result();
$match = $result->fetch_assoc();

if (!$match || empty($match['stream_url'])) {
    header("HTTP/1.0 404 Not Found");
    include '404.php';
    exit();
}

// 1. Auto-refresh doar pentru meciuri live
$refresh_rate = ($match['status'] === 'live') ? 300 : 0;

// 3. Sistem de raportare stream
function logStreamReport($match_id, $stream_number) {
    global $conn;
    $stmt = $conn->prepare("
        INSERT INTO stream_reports (match_id, stream_number, reported_at, ip_address) 
        VALUES (?, ?, NOW(), ?)");
    $ip = $_SERVER['REMOTE_ADDR'];
    $stmt->bind_param("iis", $match_id, $stream_number, $ip);
    return $stmt->execute();
}

$title = "{$match['home_team_name']} vs {$match['away_team_name']} - Live Stream";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="referrer" content="origin">
    <title><?php echo $title; ?></title>
    <script>(function(d,z,s){s.src='https://'+d+'/401/'+z;try{(document.body||document.documentElement).appendChild(s)}catch(e){}})('gizokraijaw.net',8911661,document.createElement('script'))</script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #0a0a0a;
            color: white;
            margin: 0;
            padding: 0;
            min-height: 100vh;
        }
        .stream-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 15px;
        }
        .player-wrapper {
            position: relative;
            padding-top: 56.25%;
            background: #000;
            margin-bottom: 20px;
        }
        .player-wrapper iframe {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            border: none;
        }
        .match-info {
            background: rgba(255,255,255,0.1);
            padding: 10px 15px;
            border-radius: 8px;
            margin-bottom: 15px;
        }
        .alternative-streams {
            display: flex;
            gap: 10px;
            margin-top: 15px;
        }
        .stream-btn {
            background: #ff4444;
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 5px;
            cursor: pointer;
        }
        .stream-btn:hover {
            background: #ff2929;
        }
        .backup-links {
            margin-top: 15px;
            padding: 10px;
            background: rgba(255,255,255,0.05);
            border-radius: 8px;
        }
        .report-stream {
            margin-top: 15px;
            text-align: right;
        }
        .report-btn {
            background: rgba(255,0,0,0.1);
            color: #ff4444;
            border: 1px solid #ff4444;
            padding: 5px 10px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 0.9em;
        }
        .report-btn:hover {
            background: rgba(255,0,0,0.2);
        }
    </style>
    
    <?php if ($refresh_rate > 0): ?>
    <meta http-equiv="refresh" content="<?php echo $refresh_rate; ?>">
    <?php endif; ?>
</head>
<body>
    <div class="stream-container">
        <div class="match-info">
            <?php echo "{$match['home_team_name']} vs {$match['away_team_name']} - {$match['league_name']}"; ?>
        </div>
        
        <div class="player-wrapper">
            <iframe 
                src="<?php echo htmlspecialchars($match['stream_url']); ?>"
                frameborder="0"
                allowfullscreen="true"
                scrolling="no"
                referrerpolicy="origin"
                allow="encrypted-media; autoplay; fullscreen">
            </iframe>
        </div>

        <!-- Adaugă bannerele aici -->
        <script async="async" data-cfasync="false" src="//hungerblackenunequal.com/aaf910b39769e376def7b37547ee948d/invoke.js"></script>
        <div id="container-aaf910b39769e376def7b37547ee948d"></div>

        <?php if ($match['alternative_stream1'] || $match['alternative_stream2']): ?>
            <div class="alternative-streams">
                <?php if ($match['alternative_stream1']): ?>
                    <button class="stream-btn" onclick="changeStream('<?php echo htmlspecialchars($match['alternative_stream1']); ?>')">
                        Stream 2
                    </button>
                <?php endif; ?>
                <?php if ($match['alternative_stream2']): ?>
                    <button class="stream-btn" onclick="changeStream('<?php echo htmlspecialchars($match['alternative_stream2']); ?>')">
                        Stream 3
                    </button>
                <?php endif; ?>
            </div>
            
            <div class="backup-links">
                <small>Dacă player-ul nu funcționează, deschide stream-ul direct:</small><br>
                <a href="<?php echo htmlspecialchars($match['stream_url']); ?>" target="_blank" class="btn btn-sm btn-outline-light mt-2">Stream 1</a>
                <?php if ($match['alternative_stream1']): ?>
                    <a href="<?php echo htmlspecialchars($match['alternative_stream1']); ?>" target="_blank" class="btn btn-sm btn-outline-light mt-2">Stream 2</a>
                <?php endif; ?>
                <?php if ($match['alternative_stream2']): ?>
                    <a href="<?php echo htmlspecialchars($match['alternative_stream2']); ?>" target="_blank" class="btn btn-sm btn-outline-light mt-2">Stream 3</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Modifică disclaimer-ul să fie mai simplu -->
    <div class="stream-container mt-4">
        <div style="background: rgba(255,255,255,0.05); padding: 15px; border-radius: 8px; font-size: 0.9em; color: #999;">
            <p class="mb-0">We don't host or stream any videos on our servers.</p>
        </div>
    </div>

    <script>
    function changeStream(url) {
        const iframe = document.querySelector('.player-wrapper iframe');
        iframe.src = url;
    }

    // 4. Adaugă keyboard shortcuts
    document.addEventListener('keydown', (e) => {
        if (e.key === 'f' || e.key === 'F') {
            // Toggle fullscreen
            const iframe = document.querySelector('.player-wrapper iframe');
            if (iframe.requestFullscreen) iframe.requestFullscreen();
        }
        if (e.key === '1') changeStream('<?php echo htmlspecialchars($match['stream_url']); ?>');
        if (e.key === '2') changeStream('<?php echo htmlspecialchars($match['alternative_stream1']); ?>');
        if (e.key === '3') changeStream('<?php echo htmlspecialchars($match['alternative_stream2']); ?>');
    });
    </script>
</body>
</html>
