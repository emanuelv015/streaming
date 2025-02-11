<?php
session_start();
require_once '../inc/config.php';
require_once '../inc/db_config.php';
require_once 'admin_auth.php';

// Verificare autentificare admin
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

// Preluare meciuri pentru dropdown
$matches_query = "SELECT id, home_team_name, away_team_name, match_date FROM matches ORDER BY match_date DESC";
$matches_result = $conn->query($matches_query);
?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <title>Adăugare Surse Streaming</title>
    <link rel="stylesheet" href="../assets/admin.css">
</head>
<body>
    <div class="admin-container">
        <h1>Adăugare Surse Streaming</h1>
        
        <form action="process_stream_sources.php" method="POST">
            <div class="form-group">
                <label>Meci:</label>
                <select name="match_id" required>
                    <?php while($match = $matches_result->fetch_assoc()): ?>
                        <option value="<?= $match['id'] ?>">
                            <?= $match['home_team_name'] ?> vs <?= $match['away_team_name'] ?> (<?= $match['match_date'] ?>)
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Limbă:</label>
                <select name="language" required>
                    <option value="ro">Română</option>
                    <option value="en">Engleză</option>
                    <option value="es">Spaniolă</option>
                    <option value="fr">Franceză</option>
                </select>
            </div>

            <div class="form-group">
                <label>Tip Sursă:</label>
                <select name="source_type" required>
                    <option value="iframe">iFrame</option>
                    <option value="direct_link">Link Direct</option>
                    <option value="embed">Embed</option>
                </select>
            </div>

            <div class="form-group">
                <label>URL Streaming:</label>
                <input type="text" name="stream_url" required placeholder="Introdu URL-ul sursă">
            </div>

            <div class="form-group">
                <label>Activ:</label>
                <input type="checkbox" name="is_active" value="1" checked>
            </div>

            <button type="submit" class="btn btn-primary">Adaugă Sursă</button>
        </form>
    </div>
</body>
</html>
