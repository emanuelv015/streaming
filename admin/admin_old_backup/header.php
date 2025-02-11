<?php
// Verificăm dacă suntem autentificați
require_once 'admin_auth.php';
requireAdminLogin();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - <?php echo $site_title; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css/admin.css">
    <script src="js/admin.js" defer></script>
</head>
<body>
    <div class="admin-dashboard">
        <div class="sidebar">
            <div class="logo">
                <h3>GetSportNews</h3>
            </div>
            <nav>
                <a href="index.php" <?php echo basename($_SERVER['PHP_SELF']) === 'index.php' ? 'class="active"' : ''; ?>>
                    <i class="fas fa-home"></i>
                    <span>Dashboard</span>
                </a>
                <a href="matches.php" <?php echo basename($_SERVER['PHP_SELF']) === 'matches.php' ? 'class="active"' : ''; ?>>
                    <i class="fas fa-futbol"></i>
                    <span>Matches</span>
                </a>
                <a href="teams.php" <?php echo basename($_SERVER['PHP_SELF']) === 'teams.php' ? 'class="active"' : ''; ?>>
                    <i class="fas fa-users"></i>
                    <span>Teams</span>
                </a>
                <a href="leagues.php" <?php echo basename($_SERVER['PHP_SELF']) === 'leagues.php' ? 'class="active"' : ''; ?>>
                    <i class="fas fa-trophy"></i>
                    <span>Leagues</span>
                </a>
                <a href="settings.php" <?php echo basename($_SERVER['PHP_SELF']) === 'settings.php' ? 'class="active"' : ''; ?>>
                    <i class="fas fa-cog"></i>
                    <span>Settings</span>
                </a>
                <a href="login.php?logout=1">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Logout</span>
                </a>
            </nav>
        </div>
        <div class="main-content">
