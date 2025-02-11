<?php 
session_start();
require_once __DIR__ . '/../inc/config.php';
global $site_title;

// Verifică dacă este cerere de logout
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: ' . get_url('admin/login.php'));
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($site_title); ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #ff4444;
            --bg-color: #1a1a1a;
            --text-color: #ffffff;
            --header-bg: #2a2a2a;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: var(--bg-color);
            color: var(--text-color);
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            line-height: 1.6;
        }

        .site-header {
            background: var(--header-bg);
            padding: 1rem 0;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1000;
        }

        .header-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 1rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .site-logo {
            display: flex;
            align-items: center;
            text-decoration: none;
            color: var(--text-color);
            font-size: 1.5rem;
            font-weight: bold;
        }

        .site-logo i {
            color: var(--primary-color);
            margin-right: 0.5rem;
        }

        .site-nav {
            display: flex;
            gap: 1.5rem;
        }

        .site-nav a {
            color: var(--text-color);
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            border-radius: 4px;
            transition: background-color 0.3s;
        }

        .site-nav a:hover {
            background: rgba(255,255,255,0.1);
        }

        .site-nav a.active {
            background: var(--primary-color);
        }

        .site-nav i {
            font-size: 1.2rem;
        }

        .logout-btn {
            background: #dc3545;
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 4px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            text-decoration: none;
            font-size: 0.9rem;
        }

        .logout-btn:hover {
            background: #c82333;
        }

        @media (max-width: 768px) {
            .site-nav span {
                display: none;
            }
            
            .site-nav {
                gap: 0.5rem;
            }

            .site-nav a {
                padding: 0.5rem;
            }

            .logout-btn span {
                display: none;
            }
        }
    </style>
</head>
<body>
    <header class="site-header">
        <div class="header-content">
            <a href="<?php echo get_url(); ?>" class="site-logo">
                <i class="fas fa-futbol"></i>
                <span><?php echo htmlspecialchars($site_title); ?></span>
            </a>
            <nav class="site-nav">
                <a href="<?php echo get_url(); ?>" class="<?php echo empty($_GET['page']) ? 'active' : ''; ?>">
                    <i class="fas fa-home"></i>
                    <span>Home</span>
                </a>
                <a href="<?php echo get_url('schedule'); ?>" class="<?php echo isset($_GET['page']) && $_GET['page'] === 'schedule' ? 'active' : ''; ?>">
                    <i class="fas fa-calendar"></i>
                    <span>Schedule</span>
                </a>
                <?php if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true): ?>
                <a href="<?php echo get_url('admin'); ?>" class="<?php echo isset($_GET['page']) && $_GET['page'] === 'admin' ? 'active' : ''; ?>">
                    <i class="fas fa-cog"></i>
                    <span>Admin</span>
                </a>
                <a href="?logout=1" class="logout-btn">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Logout</span>
                </a>
                <?php endif; ?>
            </nav>
        </div>
    </header>
    <div style="padding-top: 80px;"><!-- Spacing for fixed header --></div>
    <script>
        function refreshMatches() {
            const btn = event.currentTarget;
            const icon = btn.querySelector('i');
            icon.classList.add('fa-spin');
            
            // Facem request AJAX pentru a reîncărca meciurile
            fetch('/streaming/streamthunder-demo-website/get_matches.php')
                .then(response => response.text())
                .then(html => {
                    document.querySelector('.matches-container').innerHTML = html;
                    icon.classList.remove('fa-spin');
                });
        }

        document.querySelectorAll('.sport-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                if (this.classList.contains('live-tv') || this.querySelector('.fa-sync-alt')) return;
                
                // Eliminăm clasa active de pe toate butoanele
                document.querySelectorAll('.sport-btn').forEach(b => b.classList.remove('active'));
                
                // Adăugăm clasa active pe butonul apăsat
                this.classList.add('active');
                
                // Facem request AJAX pentru a încărca meciurile pentru sportul selectat
                const sport = this.dataset.sport;
                fetch(`/streaming/streamthunder-demo-website/get_matches.php?sport=${sport}`)
                    .then(response => response.text())
                    .then(html => {
                        document.querySelector('.matches-container').innerHTML = html;
                    });
            });
        });
    </script>
</body>
</html>