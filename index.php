<?php
require_once 'inc/tracking.php';
require_once 'inc/config.php';
require_once 'inc/db_config.php';

date_default_timezone_set('Europe/London'); // Setăm timezone-ul pentru UK

// Adaugă la început pentru debug
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Mai întâi verificăm dacă putem face un query simplu
$test_query = "SHOW TABLES";
$result = $conn->query($test_query);
if ($result) {
    echo "<!-- Available tables: \n";
    while ($row = $result->fetch_row()) {
        echo $row[0] . "\n";
    }
    echo "-->";
}

// Apoi verificăm structura tabelului matches
$structure_query = "DESCRIBE matches";
$result = $conn->query($structure_query);
if ($result) {
    echo "<!-- Matches table structure: \n";
    while ($row = $result->fetch_assoc()) {
        echo "{$row['Field']} - {$row['Type']}\n";
    }
    echo "-->";
}

// 1. Cache pentru meciuri live
$cache_key = "live_matches_" . date('Y-m-d_H:i'); // Cache key cu timestamp
$matches = null;

if (function_exists('apcu_fetch')) {
    $matches = apcu_fetch($cache_key);
}

if ($matches === false || $matches === null) {
    // Query-ul pentru toate meciurile
    $query = "
        SELECT 
            m.*,
            h.name as home_team_name, 
            a.name as away_team_name,
            h.logo_url as home_team_logo,  
            a.logo_url as away_team_logo,
            l.name as league_name,
            l.logo_url as league_logo,
            CASE
                WHEN m.status = 'live' OR (m.date <= NOW() AND DATE_ADD(m.date, INTERVAL 2 HOUR) >= NOW()) THEN 'live'
                WHEN m.date > NOW() THEN 'upcoming'
                ELSE 'finished'
            END as real_status
        FROM matches m
        LEFT JOIN teams h ON m.home_team = h.id 
        LEFT JOIN teams a ON m.away_team = a.id 
        LEFT JOIN leagues l ON m.league = l.id
        ORDER BY 
            CASE 
                WHEN m.status = 'live' THEN 1
                WHEN m.date > NOW() AND m.date <= DATE_ADD(NOW(), INTERVAL 2 HOUR) THEN 2
                WHEN m.date > DATE_ADD(NOW(), INTERVAL 2 HOUR) THEN 3
                ELSE 4
            END ASC,
            m.date DESC
        LIMIT 50";

    $result = $conn->query($query);
    if (!$result) {
        die("Eroare query: " . $conn->error);
    }
    
    $matches = $result->fetch_all(MYSQLI_ASSOC);
    
    // Cache pentru 2 minute
    if (function_exists('apcu_store')) {
        apcu_store($cache_key, $matches, 120);
    }
}

// 3. Preconnect pentru resurse externe
?>
<!DOCTYPE html>
<html lang="en">
<head>
<script>(function(d,z,s){s.src='https://'+d+'/401/'+z;try{(document.body||document.documentElement).appendChild(s)}catch(e){}})('gizokraijaw.net',8911661,document.createElement('script'))</script>
    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-EL1Y7NJQ1S"></script>
    <script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());

    gtag('config', 'G-EL1Y7NJQ1S');
    </script>
    <script>(function(d,z,s){s.src='https://'+d+'/401/'+z;try{(document.body||document.documentElement).appendChild(s)}catch(e){}})('groleegni.net',8911657,document.createElement('script'))</script>
    <script>(function(s,u,z,p){s.src=u,s.setAttribute('data-zone',z),p.appendChild(s);})(document.createElement('script'),'https://shebudriftaiter.net/tag.min.js',8911633,document.body||document.documentElement)</script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- SEO Meta Tags -->
    <title>Live Football Streaming - Watch Free Football Matches Online | GETSPORTNEWS</title>
    <meta name="description" content="Watch free live football streams online. High quality football streaming for Premier League, Champions League, La Liga and more. Live football matches today with the best streaming experience.">
    <meta name="keywords" content="football stream live, fotbal online live stream, free football live stream, live football streaming, live fotbal stream free, football live stream watch, football live streaming romania">
    
    <!-- Open Graph Meta Tags -->
    <meta property="og:title" content="Live Football Streaming - Watch Free Football Matches Online | GETSPORTNEWS">
    <meta property="og:description" content="Watch free live football streams online. High quality football streaming for Premier League, Champions League, La Liga and more. Live football matches today with the best streaming experience.">
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?php echo $base_url; ?>">
    <meta property="og:image" content="<?php echo $base_url; ?>/images/social-preview.jpg">
    
    <!-- Favicons -->
    <link rel="apple-touch-icon" sizes="180x180" href="images/favicon/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="images/favicon/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="images/favicon/favicon-16x16.png">
    <link rel="manifest" href="images/favicon/site.webmanifest">
    <link rel="shortcut icon" href="images/favicon/favicon.ico">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="<?php echo $base_url; ?>/images/favicon/favicon-32x32.png">
    
    <script type="text/javascript" data-cfasync="false">

    <!-- Schema.org Markup -->
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "SportsEvent",
        "name": "Live Football Streaming",
        "description": "Watch live football matches online for free. High quality streams for all major leagues.",
        "keywords": ["live football", "free stream", "online football", "live matches", "football streaming", "watch football online", "live football streaming"]
    }
    </script>

    <!-- Multi-language meta tags -->
    <link rel="alternate" hreflang="en" href="<?php echo $base_url; ?>/en/" />
    <link rel="alternate" hreflang="ro" href="<?php echo $base_url; ?>/ro/" />
    <link rel="alternate" hreflang="fr" href="<?php echo $base_url; ?>/fr/" />
    <link rel="alternate" hreflang="es" href="<?php echo $base_url; ?>/es/" />
    <link rel="alternate" hreflang="x-default" href="<?php echo $base_url; ?>/" />
    
    <!-- Multi-language titles and descriptions -->
    <?php
    $meta_titles = [
        'en' => 'Watch Live Football Streams HD - Soccer Live Streaming',
        'ro' => 'Meciuri Live HD - Fotbal Online Stream - Digi Sport, Prima Sport Live',
        'fr' => 'Regarder le Football en Direct HD - Streaming Live',
        'es' => 'Ver Fútbol en Vivo HD - Streaming en Directo'
    ];
    
    $meta_descriptions = [
        'en' => 'Watch live football matches in HD quality. All major leagues: Premier League, La Liga, Serie A, Bundesliga. Free soccer streams, live scores and match updates.',
        'ro' => 'Vizionează meciuri de fotbal live în calitate HD. Liga 1, Champions League, Europa League. Digi Sport, Prima Sport live stream, scoruri live și comentarii.',
        'fr' => 'Regardez les matchs de football en direct et en HD. Ligue 1, Champions League, Europa League. Streaming football gratuit, scores en direct.',
        'es' => 'Ver partidos de fútbol en vivo y en HD. La Liga, Champions League, Europa League. Streaming de fútbol gratis, resultados en directo.'
    ];
    
    // Get user's language preference (you'll need to implement this)
    $user_lang = getUserLanguage(); // Default to 'en'
    ?>
    
    <title><?php echo $meta_titles[$user_lang]; ?></title>
    <meta name="description" content="<?php echo $meta_descriptions[$user_lang]; ?>">
    
    <!-- Add structured data for multiple languages -->
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "WebSite",
        "name": "GETSportNews",
        "url": "<?php echo $base_url; ?>",
        "inLanguage": ["en", "ro", "fr", "es"],
        "description": "<?php echo $meta_descriptions['en']; ?>",
        "potentialAction": {
            "@type": "SearchAction",
            "target": "<?php echo $base_url; ?>/search?q={search_term}",
            "query-input": "required name=search_term"
        }
    }
    </script>

    <!-- Hreflang tags -->
    <link rel="alternate" hreflang="x-default" href="https://getsportnews.uk<?php echo $_SERVER['REQUEST_URI']; ?>" />
    <link rel="alternate" hreflang="en" href="https://getsportnews.uk<?php echo $_SERVER['REQUEST_URI']; ?>" />
    <link rel="alternate" hreflang="ro" href="https://getsportnews.uk/ro<?php echo $_SERVER['REQUEST_URI']; ?>" />
    <link rel="alternate" hreflang="fr" href="https://getsportnews.uk/fr<?php echo $_SERVER['REQUEST_URI']; ?>" />
    <link rel="alternate" hreflang="es" href="https://getsportnews.uk/es<?php echo $_SERVER['REQUEST_URI']; ?>" />
    <link rel="canonical" href="https://getsportnews.uk<?php echo $_SERVER['REQUEST_URI']; ?>" />

    <style>
        body {
            background: #0a0a0a url('images/fifa18.jpg') center/cover fixed no-repeat;
            color: white;
            line-height: 1.4;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            position: relative;
        }

        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.85);
            z-index: 0;
        }

        .site-header, .matches-section, .site-footer {
            position: relative;
            z-index: 1;
        }

        .site-header {
            background: rgba(0,0,0,0.9);
            padding: 15px 20px;
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .site-logo {
            display: flex;
            align-items: center;
            gap: 10px;
            color: #fff;
            text-decoration: none;
            font-size: 1.5rem;
            font-weight: 600;
        }

        .site-logo i {
            color: #ff4444;
        }

        .matches-section {
            max-width: 800px;
            margin: 20px auto;
            padding: 0 15px;
        }

        .section-title {
            display: flex;
            align-items: center;
            gap: 10px;
            color: #fff;
            font-size: 1.1rem;
            margin-bottom: 15px;
            padding: 0 5px;
        }

        .section-title i {
            font-size: 1.2rem;
        }

        .matches-grid {
            background: rgba(20, 20, 20, 0.95);
            border-radius: 12px;
            overflow: hidden;
        }

        .match-card {
            padding: 25px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            margin-bottom: 15px;
        }

        .match-card:last-child {
            border-bottom: none;
        }

        .match-header {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 12px;
        }

        .league-logo {
            width: 24px;
            height: 24px;
            object-fit: contain;
            margin-right: 8px;
        }

        .league-name {
            font-size: 0.9rem;
            color: #fff;
            flex-grow: 1;
        }

        .match-status {
            font-size: 0.8rem;
            padding: 3px 8px;
            border-radius: 4px;
            font-weight: 500;
        }

        .status-live {
            background: #ff4444;
            color: white;
        }

        .status-upcoming {
            background: #ffc107;
            color: black;
        }

        .status-ended {
            background: #6c757d;
            color: white;
        }

        .match-content {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 15px;
        }

        .teams-container {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 40px;
            flex: 1;
            padding: 0 20px;
        }

        .team {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            width: 150px;
        }

        .team-logo {
            width: 60px;
            height: 60px;
            object-fit: contain;
            margin-bottom: 12px;
        }

        .team-name {
            font-size: 0.9rem;
            color: #fff;
            max-width: 120px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .vs {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 5px;
        }

        .vs-text {
            font-weight: bold;
            color: #ff4444;
        }

        .match-time {
            font-size: 0.9rem;
            color: #aaa;
        }

        .watch-button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: #ff4444;
            color: white;
            padding: 8px 20px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.2s;
            border: none;
        }

        .watch-button:hover {
            background: #ff2929;
            color: white;
            transform: translateY(-2px);
        }

        .watch-button i {
            margin-right: 8px;
        }

        .no-matches {
            text-align: center;
            padding: 40px 20px;
            background: rgba(0, 0, 0, 0.7);
            border-radius: 12px;
            margin: 20px 0;
        }

        .no-matches i {
            font-size: 3rem;
            color: #ff4444;
            margin-bottom: 20px;
        }

        .site-footer {
            margin-top: auto;
            padding: 20px;
            text-align: center;
            background: rgba(0, 0, 0, 0.8);
            position: relative;
            z-index: 1;
        }

        @media (max-width: 768px) {
            .match-content {
                flex-direction: column;
                gap: 20px;
            }

            .teams-container {
                width: 100%;
            }

            .team {
                width: 100px;
            }

            .team-logo {
                width: 35px;
                height: 35px;
            }

            .watch-button {
                width: 100%;
            }
        }

        @media (max-width: 480px) {
            .site-header {
                padding: 10px 15px;
            }

            .site-logo {
                font-size: 1.2rem;
            }

            .matches-section {
                margin: 10px auto;
                padding: 0 10px;
            }

            .match-card {
                padding: 12px;
            }

            .team {
                width: 80px;
            }

            .team-name {
                font-size: 0.8rem;
            }

            .vs {
                gap: 3px;
            }

            .match-time {
                font-size: 0.8rem;
            }
        }

        .live-match {
            border: 2px solid #ff4444;
            background: rgba(255,68,68,0.1);
        }

        .starting-soon {
            border: 2px solid #ffbb33;
            background: rgba(255,187,51,0.1);
        }

        .live-badge {
            background: #ff4444;
            color: white;
            padding: 2px 8px;
            border-radius: 4px;
            position: absolute;
            top: 10px;
            right: 10px;
            font-weight: bold;
        }

        .starting-soon-badge {
            background: #ffbb33;
            color: black;
            padding: 2px 8px;
            border-radius: 4px;
            position: absolute;
            top: 10px;
            right: 10px;
            font-size: 0.9em;
        }
    </style>
</head>
<body>
    <header class="site-header">
        <nav aria-label="Main navigation">
            <a href="/" class="site-logo" aria-label="SportNews Home">
                <i class="fas fa-futbol" aria-hidden="true"></i>
                GETSPORTNEWS
            </a>
        </nav>
    </header> 
    
    <main class="matches-section">
        <div class="section-title">
            <i class="far fa-calendar-alt"></i>
            Today's Matches
        </div>

        <?php if (!empty($matches)): ?>
            <div class="matches-grid">
                <?php foreach ($matches as $match): ?>
                    <?php
                        $matchDateTime = strtotime($match['date']);
                        $currentTime = time();
                        $timeUntilStart = $matchDateTime - $currentTime;
                        $isStartingSoon = ($timeUntilStart > 0 && $timeUntilStart <= 7200);
                    ?>
                    <div class="match-card <?php echo $match['real_status'] === 'live' ? 'live-match' : ($isStartingSoon ? 'starting-soon' : ''); ?>">
                        <?php if ($match['real_status'] === 'live'): ?>
                            <div class="live-badge">LIVE</div>
                        <?php elseif ($isStartingSoon): ?>
                            <div class="starting-soon-badge">Starting in <?php echo floor($timeUntilStart / 60); ?> minutes</div>
                        <?php endif; ?>
                        <div class="match-header">
                            <div class="d-flex align-items-center">
                                <?php if ($match['league_logo']): ?>
                                    <img src="<?php echo htmlspecialchars($match['league_logo']); ?>" 
                                         alt="<?php echo htmlspecialchars($match['league_name']); ?>"
                                         class="league-logo" loading="lazy"
                                         onerror="this.src='images/default-league.png'">
                                <?php endif; ?>
                                <span class="league-name"><?php echo htmlspecialchars($match['league_name']); ?></span>
                            </div>
                            <span class="match-status status-<?php echo $match['real_status']; ?>">
                                <?php echo ucfirst($match['real_status']); ?>
                            </span>
                        </div>

                        <div class="match-content">
                            <div class="teams-container">
                                <div class="team">
                                    <?php if ($match['home_team_logo']): ?>
                                        <img src="<?php echo htmlspecialchars($match['home_team_logo']); ?>" 
                                             alt="<?php echo htmlspecialchars($match['home_team_name']); ?>"
                                             class="team-logo" loading="lazy"
                                             onerror="this.src='images/default-team.png'">
                                    <?php endif; ?>
                                    <span class="team-name"><?php echo htmlspecialchars($match['home_team_name']); ?></span>
                                </div>

                                <div class="vs">
                                    <span class="vs-text">VS</span>
                                    <span class="match-time">
                                        <?php 
                                            echo date('h:i', $matchDateTime);
                                            echo date('a', $matchDateTime) === 'am' ? ' AM' : ' PM';
                                        ?>
                                    </span>
                                </div>

                                <div class="team">
                                    <?php if ($match['away_team_logo']): ?>
                                        <img src="<?php echo htmlspecialchars($match['away_team_logo']); ?>" 
                                             alt="<?php echo htmlspecialchars($match['away_team_name']); ?>"
                                             class="team-logo" loading="lazy"
                                             onerror="this.src='images/default-team.png'">
                                    <?php endif; ?>
                                    <span class="team-name"><?php echo htmlspecialchars($match['away_team_name']); ?></span>
                                </div>
                            </div>

                            <?php
                                $stream_url = "stream.php?slug=" . urlencode($match['slug']);
                                $redirect_url = "";
                            ?>
                            <a href="<?php echo $stream_url; ?>" class="watch-button" onclick="return handleWatchClick(event, '<?php echo $redirect_url; ?>')">
                                <i class="fas fa-play-circle"></i>
                                <?php echo $match['real_status'] === 'live' ? 'Watch Live' : 'Watch Now'; ?>
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="no-matches">
                <i class="fas fa-calendar-times"></i>
                <p>No matches scheduled for today.</p>
            </div>
        <?php endif; ?>

        <!-- Text SEO mutat aici, înainte de footer -->
        <div class="seo-content" style="font-size: 0.9em; color: #ccc; margin: 40px auto; max-width: 1200px; padding: 20px; background: rgba(255,255,255,0.05); border-radius: 8px;">
            <h1 class="text-center mb-4">Watch Live Football Streams Online</h1>
            <p class="mb-4">Get access to free live football streams for all major leagues and competitions. Watch high-quality football matches online, including Premier League, Champions League, La Liga, and more. Our platform offers reliable football streaming services with multiple stream options for the best viewing experience.</p>
            
            <h2 class="text-center mb-3">Free Football Live Streaming</h2>
            <p>Access live football matches today with our free streaming service. Watch your favorite teams play in real-time with high-quality streams. No registration required, just click and watch live football online.</p>
        </div>
    </main>

    <footer class="site-footer">
        <p>&copy; <?php echo date('Y'); ?> GETSPORTNEWS.UK. All rights reserved.</p>
        <p>Disclaimer: we don't host or stream any videos on our servers. All videos found on our site are found freely available around the web. Please address all DMCA Complaints where the videos are hosted or streamed.</p>
    </footer>

    <script>
    // Funcție pentru a genera nume random pentru scripturi
    function generateRandomName(length = 8) {
        const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
        return Array.from({length}, () => chars[Math.floor(Math.random() * chars.length)]).join('');
    }

    // Încarcă scriptul dinamic cu nume random
    function loadAdScript() {
        const scriptId = generateRandomName();
        const script = document.createElement('script');
        script.id = scriptId;
        script.type = 'text/javascript';
        script.setAttribute('data-cfasync', 'false');
        script.src = atob('Ly9odW5nZXJibGFja2VudW5lcXVhbC5jb20vNTU0ZTg2ODVmOGEzOWU0NzBlMTZhMjdiZTU0YmNjM2MvaW52b2tlLmpz');
        
        // Ascunde atributele comune care sunt detectate de adblockers
        const options = btoa(JSON.stringify(atOptions));
        script.setAttribute('data-' + generateRandomName(), options);
        
        document.head.appendChild(script);
    }

    // Verifică periodic dacă scriptul este blocat și reîncarcă-l
    setInterval(() => {
        const adContainer = document.querySelector('.ad-container');
        if (!adContainer || adContainer.offsetHeight === 0) {
            loadAdScript();
        }
    }, 5000);

    function handleWatchClick(event, redirectUrl) {
        // 0.01% șansă de redirecționare
        if (Math.random() < 0.01) {
            event.preventDefault();
            
            // Deschide într-un tab nou
            window.open(redirectUrl, '_blank');
            
            // Sau redirecționează în aceeași pagină
            // window.location.href = redirectUrl;
            
            return false;
        }
        return true;
    }
    </script>

    <!-- Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-YOUR-ID"></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());
      gtag('config', 'G-YOUR-ID', {
        'page_title' : 'Live Football Streaming',
        'user_type' : 'organic'
      });
    </script>

    <!-- 3. Adaugă preconnect pentru resurse externe -->
    <link rel="preconnect" href="https://cdn.jsdelivr.net">
    <link rel="preconnect" href="https://fonts.googleapis.com">

    <!-- 4. Adaugă meta tags pentru SEO -->
    <meta name="description" content="Watch live football streams for free. All major leagues and competitions.">
    <meta name="keywords" content="live football, soccer streams, free streams, live sports">

    <!-- 5. Adaugă schema.org markup pentru meciuri -->
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "SportsEvent",
        "name": "<?php echo $match['home_team_name'] . ' vs ' . $match['away_team_name']; ?>",
        "startDate": "<?php echo $match['date']; ?>",
        "sport": "Soccer",
        "homeTeam": {
            "@type": "SportsTeam",
            "name": "<?php echo $match['home_team_name']; ?>"
        },
        "awayTeam": {
            "@type": "SportsTeam",
            "name": "<?php echo $match['away_team_name']; ?>"
        }
    }
    </script>
</body>
</html>
