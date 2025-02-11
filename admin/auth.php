<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include database config
require_once(__DIR__ . '/../inc/db_config.php');

// Function to verify login
function verifyLogin($username, $password) {
    global $conn;
    
    $stmt = $conn->prepare("SELECT * FROM admin_users WHERE username = ? AND password = ?");
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_username'] = $username;
        return true;
    }
    return false;
}

// Function to check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}

// Function to require login
function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: login.php');
        exit();
    }
}

// Function to logout
function logout() {
    session_destroy();
    header('Location: login.php');
    exit();
}
?>
