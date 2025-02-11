<?php
session_start();
require_once '../inc/config.php';
require_once '../inc/db_config.php';
require_once 'admin_auth.php';

// Verifică autentificarea
requireAdminLogin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $name = $_POST['name'];
        $logo_url = $_POST['logo_url'];

        $query = "INSERT INTO teams (name, logo_url) VALUES (?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('ss', $name, $logo_url);

        if ($stmt->execute()) {
            header('Location: teams.php?success=1');
            exit();
        } else {
            throw new Exception("Failed to add team");
        }
    } catch (Exception $e) {
        header('Location: add_team.php?error=' . urlencode($e->getMessage()));
        exit();
    }
} 