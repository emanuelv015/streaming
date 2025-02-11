<?php
require_once 'inc/config.php';
require_once 'inc/db_config.php';
session_start();

$match_id = isset($_GET['match_id']) ? (int)$_GET['match_id'] : 0;

// Verificăm dacă meciul există și dacă timpul este potrivit
$sql = "SELECT * FROM matches WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $match_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $match = $result->fetch_assoc();
    $matchDateTime = strtotime($match['match_date'] . ' ' . $match['match_time']);
    $currentTime = time();
    $timeUntilMatch = $matchDateTime - $currentTime;
    
    if ($timeUntilMatch > 3600) {
        // Redirecționăm înapoi la index cu un mesaj
        header("Location: index.php?error=stream_not_available");
        exit();
    }
    
    // Aici continuă codul pentru afișarea stream-ului
    // Get match data
    $slug = $_GET['slug'] ?? '';
    $query = "
        SELECT 
            m.*, 
            h.name as home_team_name, 
            a.name as away_team_name,
            h.logo_url as home_team_logo,
            a.logo_url as away_team_logo,
            l.name as league_name,
            l.logo_url as league_logo
        FROM matches m
        LEFT JOIN teams h ON m.home_team = h.id 
        LEFT JOIN teams a ON m.away_team = a.id 
        LEFT JOIN leagues l ON m.league = l.id
        WHERE m.slug = ? AND m.status IN ('upcoming', 'live')";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $slug);
    $stmt->execute();
    $result = $stmt->get_result();
    $match = $result->fetch_assoc();

    if (!$match) {
        header("HTTP/1.0 404 Not Found");
        include '404.php';
        exit();
    }

    // Verifică dacă meciul poate fi accesat (cu o oră înainte)
    $matchDateTime = strtotime($match['date']);
    $currentTime = time();
    $timeUntilMatch = $matchDateTime - $currentTime;

    if ($timeUntilMatch > 3600) {
        $hours = floor($timeUntilMatch / 3600);
        $minutes = floor(($timeUntilMatch % 3600) / 60);
        
        // Redirecționare cu mesaj
        $_SESSION['stream_error'] = [
            'message' => "Stream will be available 1 hour before the match start.",
            'match_time' => date('d M Y H:i', $matchDateTime),
            'time_left' => sprintf("%d hours and %d minutes", $hours, $minutes)
        ];
        header("Location: index.php");
        exit();
    }

    // Înregistrează vizita în statistici
    $stmt = $conn->prepare("
        INSERT INTO user_actions (session_id, action_type, match_id, page_url) 
        VALUES (?, 'watch', ?, ?)
    ");
    $session_id = session_id();
    $current_url = $_SERVER['REQUEST_URI'];
    $stmt->bind_param("sis", $session_id, $match['id'], $current_url);
    $stmt->execute();

    // Actualizează statisticile de stream
    $today = date('Y-m-d');
    $stmt = $conn->prepare("
        INSERT INTO stream_stats (match_id, views, created_at) 
        VALUES (?, 1, ?) 
        ON DUPLICATE KEY UPDATE views = views + 1
    ");
    $stmt->bind_param("is", $match['id'], $today);
    $stmt->execute();

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
    <?php
} else {
    header("Location: index.php?error=match_not_found");
    exit();
}

$conn->close();
?>
