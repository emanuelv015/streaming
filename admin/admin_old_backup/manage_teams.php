<?php
include __DIR__ . '/../inc/config.php';
include __DIR__ . '/../inc/db_config.php';
check_admin_access(); // Adaugă această linie la începutul fiecărui fișier

// Procesăm adăugarea unei noi echipe
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] == 'add') {
        $name = $_POST['name'];
        $logo_url = $_POST['logo_url'];
        
        $stmt = $conn->prepare("INSERT INTO teams (name, logo_url) VALUES (?, ?)");
        $stmt->bind_param("ss", $name, $logo_url);
        
        if ($stmt->execute()) {
            echo "<div class='alert alert-success'>Echipa a fost adăugată cu succes!</div>";
        } else {
            echo "<div class='alert alert-danger'>Eroare la adăugarea echipei: " . $stmt->error . "</div>";
        }
    } elseif ($_POST['action'] == 'delete' && isset($_POST['id'])) {
        // Verificăm dacă echipa este folosită în meciuri
        $stmt = $conn->prepare("SELECT COUNT(*) as count FROM matches WHERE home_team_id = ? OR away_team_id = ?");
        $stmt->bind_param("ii", $_POST['id'], $_POST['id']);
        $stmt->execute();
        $result = $stmt->get_result();
        $count = $result->fetch_assoc()['count'];
        
        if ($count > 0) {
            echo "<div class='alert alert-danger'>Nu se poate șterge echipa deoarece este folosită în " . $count . " meciuri!</div>";
        } else {
            $stmt = $conn->prepare("DELETE FROM teams WHERE id = ?");
            $stmt->bind_param("i", $_POST['id']);
            if ($stmt->execute()) {
                echo "<div class='alert alert-success'>Echipa a fost ștearsă cu succes!</div>";
            } else {
                echo "<div class='alert alert-danger'>Eroare la ștergerea echipei: " . $stmt->error . "</div>";
            }
        }
    } elseif ($_POST['action'] == 'edit' && isset($_POST['id'])) {
        $stmt = $conn->prepare("UPDATE teams SET name = ?, logo_url = ? WHERE id = ?");
        $stmt->bind_param("ssi", $_POST['name'], $_POST['logo_url'], $_POST['id']);
        if ($stmt->execute()) {
            echo "<div class='alert alert-success'>Echipa a fost actualizată cu succes!</div>";
        } else {
            echo "<div class='alert alert-danger'>Eroare la actualizarea echipei: " . $stmt->error . "</div>";
        }
    }
}

// Preluăm toate echipele
$teams = $conn->query("SELECT * FROM teams ORDER BY name ASC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestionare Echipe - Admin Panel</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #1a1a1a;
            color: #fff;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: rgba(0, 0, 0, 0.4);
            padding: 30px;
            border-radius: 12px;
        }
        h1 {
            text-align: center;
            margin-bottom: 30px;
            color: #fff;
        }
        .teams-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        .teams-table th,
        .teams-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        .teams-table th {
            background: rgba(0, 0, 0, 0.2);
            font-weight: 600;
        }
        .teams-table tr:hover {
            background: rgba(255, 255, 255, 0.05);
        }
        .btn {
            display: inline-block;
            padding: 8px 16px;
            border-radius: 6px;
            font-weight: 500;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        .btn-primary {
            background: rgba(255, 85, 41, 0.9);
            color: #fff;
            border: none;
        }
        .btn-danger {
            background: rgba(220, 53, 69, 0.9);
            color: #fff;
            border: none;
        }
        .btn:hover {
            transform: translateY(-2px);
            opacity: 1;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
        }
        input[type="text"] {
            width: 100%;
            padding: 10px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 6px;
            background: rgba(0, 0, 0, 0.4);
            color: #fff;
            font-size: 14px;
        }
        .add-team-form {
            background: rgba(0, 0, 0, 0.2);
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
        }
        .team-logo {
            width: 40px;
            height: 40px;
            object-fit: contain;
        }
        .alert {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
        }
        .alert-success {
            background: rgba(40, 167, 69, 0.2);
            border: 1px solid #28a745;
            color: #28a745;
        }
        .alert-danger {
            background: rgba(220, 53, 69, 0.2);
            border: 1px solid #dc3545;
            color: #dc3545;
        }
        .nav-links {
            text-align: center;
            margin-bottom: 30px;
        }
        .nav-links a {
            color: #fff;
            text-decoration: none;
            margin: 0 10px;
            padding: 8px 16px;
            border-radius: 6px;
            background: rgba(255, 255, 255, 0.1);
            transition: all 0.3s ease;
        }
        .nav-links a:hover {
            background: rgba(255, 85, 41, 0.9);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="nav-links">
            <a href="index.php">Admin Panel</a>
            <a href="add_event.php">Adaugă Meci</a>
            <a href="manage_leagues.php">Gestionează Ligi</a>
            <a href="manage_teams.php">Gestionează Echipe</a>
        </div>

        <h1>Gestionare Echipe</h1>
        
        <div class="add-team-form">
            <h3>Adaugă Echipă Nouă</h3>
            <form method="post">
                <input type="hidden" name="action" value="add">
                <div class="form-group">
                    <label>Nume Echipă:</label>
                    <input type="text" name="name" required placeholder="Ex: Real Madrid">
                </div>
                <div class="form-group">
                    <label>URL Logo:</label>
                    <input type="text" name="logo_url" required placeholder="Ex: https://example.com/logo.png">
                </div>
                <button type="submit" class="btn btn-primary">Adaugă Echipă</button>
            </form>
        </div>

        <table class="teams-table">
            <thead>
                <tr>
                    <th>Logo</th>
                    <th>Nume Echipă</th>
                    <th>URL Logo</th>
                    <th>Acțiuni</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($team = $teams->fetch_assoc()): ?>
                <tr>
                    <td>
                        <img src="<?php echo htmlspecialchars($team['logo_url']); ?>" 
                             alt="<?php echo htmlspecialchars($team['name']); ?>"
                             class="team-logo">
                    </td>
                    <td><?php echo htmlspecialchars($team['name']); ?></td>
                    <td><?php echo htmlspecialchars($team['logo_url']); ?></td>
                    <td>
                        <form method="post" style="display: inline;">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="id" value="<?php echo $team['id']; ?>">
                            <button type="submit" class="btn btn-danger" onclick="return confirm('Ești sigur că vrei să ștergi această echipă?')">Șterge</button>
                        </form>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
