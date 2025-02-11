<?php
// Dezactivăm afișarea erorilor pentru a preveni dezvăluirea informațiilor
error_reporting(0);
@ini_set('display_errors', 0);

session_start();
require_once '../inc/config.php';

// Protecție suplimentară pentru a preveni accesarea directă a fișierului
if (basename($_SERVER['PHP_SELF']) === 'admin_auth.php') {
    header('HTTP/1.0 403 Forbidden');
    exit('Access forbidden');
}

// Verifică dacă utilizatorul este autentificat
function isAdminLoggedIn() {
    return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}

// Redirecționează către login dacă nu este autentificat
function requireAdminLogin() {
    if (!isAdminLoggedIn()) {
        header('Location: ' . get_url('admin/login.php'));
        exit;
    }
}

// Check if user is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: ' . get_url('admin/login.php'));
    exit;
}

// Handle logout
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: ' . get_url('admin/login.php'));
    exit;
}
?>