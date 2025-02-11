<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GSN Admin</title>
    
    <!-- PWA Meta Tags -->
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="GSN Admin">
    <meta name="application-name" content="GSN Admin">
    <meta name="msapplication-TileColor" content="#343a40">
    <meta name="theme-color" content="#343a40">
    <meta name="msapplication-navbutton-color" content="#343a40">
    
    <!-- Add to homescreen for Chrome on Android -->
    <meta name="mobile-web-app-capable" content="yes">
    
    <!-- Add to homescreen for Safari on iOS -->
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="GSN Admin">
    
    <!-- Icons -->
    <link rel="icon" type="image/png" sizes="192x192" href="/admin/images/icon-192.png">
    <link rel="apple-touch-icon" href="/admin/images/icon-192.png">
    <link rel="manifest" href="/admin/manifest.json">

    <!-- Existing CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
    <!-- Adaugă după navbar -->
    <div class="install-app-banner" style="background: linear-gradient(45deg, #343a40, #1a1a1a); margin: 20px; padding: 15px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.2);">
        <div class="d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-3">
                <div style="background: #ff4444; padding: 12px; border-radius: 8px;">
                    <i class="bi bi-phone-fill" style="font-size: 24px; color: white;"></i>
                </div>
                <div>
                    <h5 class="mb-1" style="color: white;">Admin Panel App</h5>
                    <p class="mb-0 text-light" style="opacity: 0.8;">Install for quick access</p>
                </div>
            </div>
            <div class="d-flex align-items-center gap-2">
                <button onclick="installPWA()" class="btn btn-primary">
                    <i class="bi bi-download"></i> Install App
                </button>
                <div class="text-light ms-2" style="font-size: 24px;">
                    <i class="bi bi-arrow-up-circle-fill" style="animation: bounce 1s infinite;"></i>
                </div>
            </div>
        </div>
    </div>

    <script>
    let deferredPrompt;

    window.addEventListener('beforeinstallprompt', (e) => {
        e.preventDefault();
        deferredPrompt = e;
        
        // Arată prompt-ul de instalare
        document.querySelector('.install-prompt').style.display = 'block';
    });

    function installPWA() {
        const promptElement = document.querySelector('.install-prompt');
        if (deferredPrompt) {
            deferredPrompt.prompt();
            deferredPrompt.userChoice.then((choiceResult) => {
                if (choiceResult.outcome === 'accepted') {
                    promptElement.style.display = 'none';
                }
                deferredPrompt = null;
            });
        }
    }

    // Ascunde prompt-ul dacă aplicația este deja instalată
    window.addEventListener('appinstalled', (evt) => {
        document.querySelector('.install-prompt').style.display = 'none';
    });
    </script>

    <style>
    @keyframes bounce {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-5px); }
    }
    </style>

    <div class="col-md-2 sidebar">
        <h4 class="text-white text-center mb-4">Admin Panel</h4>
        <nav>
            <a href="index.php" class="nav-link">
                <i class="bi bi-house-door"></i> Dashboard
            </a>
            <a href="matches.php" class="nav-link">
                <i class="bi bi-controller"></i> Matches
            </a>
            <a href="leagues.php" class="nav-link">
                <i class="bi bi-trophy"></i> Leagues
            </a>
            <a href="teams.php" class="nav-link">
                <i class="bi bi-people"></i> Teams
            </a>

            <!-- Adaugă acest buton pentru instalare -->
            <div class="mt-4 px-3">
                <button onclick="installPWA()" class="btn btn-warning w-100 text-start">
                    <i class="bi bi-phone"></i> Install Admin App
                    <div class="small text-dark mt-1">Quick access on mobile</div>
                </button>
            </div>

            <a href="logout.php" class="nav-link text-danger mt-5">
                <i class="bi bi-box-arrow-right"></i> Logout
            </a>
        </nav>
    </div>
</body>
</html> 