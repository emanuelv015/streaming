<?php
session_start();
require_once '../inc/config.php';
require_once '../inc/db_config.php';
require_once 'admin_auth.php';

// Verifică autentificarea
requireAdminLogin();

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $logo_url = $_POST['logo_url'];
    
    // Generate slug from name
    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name)));
    
    // Insert into database
    $stmt = $conn->prepare("INSERT INTO teams (name, slug, logo_url) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $name, $slug, $logo_url);
    
    if ($stmt->execute()) {
        header('Location: ' . get_url('admin/teams.php'));
        exit;
    } else {
        $error = "Error adding team: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Team</title>
    <link rel="stylesheet" href="<?php echo get_url('admin/css/style.css'); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        /* Aceleași stiluri ca la add_match.php */
    </style>
</head>
<body>
    <div class="form-container">
        <h2>Add New Team</h2>
        
        <?php if (isset($error)): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="form-group">
                <label for="name">Team Name</label>
                <input type="text" id="name" name="name" required>
            </div>

            <div class="form-group">
                <label for="logo_url">Team Logo URL</label>
                <input type="url" id="logo_url" name="logo_url" required>
            </div>

            <div class="form-group">
                <button type="submit">Add Team</button>
                <a href="<?php echo get_url('admin/teams.php'); ?>" class="button">Cancel</a>
            </div>
        </form>
    </div>
</body>
</html>