<?php
include __DIR__ . '/../inc/config.php';
include __DIR__ . '/../inc/db_config.php';
check_admin_access(); 

// Verificăm dacă există tabelul leagues
$sql = "CREATE TABLE IF NOT EXISTS leagues (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    country_code VARCHAR(10) NOT NULL,
    display_name VARCHAR(100) NOT NULL,
    category VARCHAR(50) NOT NULL,
    sort_order INT NOT NULL DEFAULT 0
)";
$conn->query($sql);

// Adăugăm ligi implicite dacă tabelul e gol
$result = $conn->query("SELECT COUNT(*) as count FROM leagues");
$row = $result->fetch_assoc();
if ($row['count'] == 0) {
    $default_leagues = [
        // Competiții Europene
        ['Champions League', 'eu', 'UEFA: Champions League', 'European', 1],
        ['Europa League', 'eu', 'UEFA: Europa League', 'European', 2],
        ['Conference League', 'eu', 'UEFA: Conference League', 'European', 3],
        // Anglia
        ['Premier League', 'gb-eng', 'ANGLIA: Premier League', 'National', 4],
        ['Championship', 'gb-eng', 'ANGLIA: Championship', 'National', 5],
        // Spania
        ['La Liga', 'es', 'SPANIA: La Liga', 'National', 6],
        ['La Liga 2', 'es', 'SPANIA: La Liga 2', 'National', 7],
        // Italia
        ['Serie A', 'it', 'ITALIA: Serie A', 'National', 8],
        ['Serie B', 'it', 'ITALIA: Serie B', 'National', 9],
        // Germania
        ['Bundesliga', 'de', 'GERMANIA: Bundesliga', 'National', 10],
        ['Bundesliga 2', 'de', 'GERMANIA: Bundesliga 2', 'National', 11],
        // Franța
        ['Ligue 1', 'fr', 'FRANȚA: Ligue 1', 'National', 12],
        ['Ligue 2', 'fr', 'FRANȚA: Ligue 2', 'National', 13],
        // România
        ['Superliga', 'ro', 'ROMÂNIA: Superliga', 'National', 14],
        ['Liga 2', 'ro', 'ROMÂNIA: Liga 2', 'National', 15],
        // Alte țări
        ['Eredivisie', 'nl', 'OLANDA: Eredivisie', 'National', 16],
        ['Primeira Liga', 'pt', 'PORTUGALIA: Primeira Liga', 'National', 17],
        ['Super Lig', 'tr', 'TURCIA: Super Lig', 'National', 18]
    ];

    $stmt = $conn->prepare("INSERT INTO leagues (name, country_code, display_name, category, sort_order) VALUES (?, ?, ?, ?, ?)");
    foreach ($default_leagues as $league) {
        $stmt->bind_param("ssssi", $league[0], $league[1], $league[2], $league[3], $league[4]);
        $stmt->execute();
    }
}

// Procesăm adăugarea unei noi ligi
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] == 'add') {
        $stmt = $conn->prepare("INSERT INTO leagues (name, country_code, display_name, category, sort_order) VALUES (?, ?, ?, ?, ?)");
        $sort_order = $conn->query("SELECT MAX(sort_order) + 1 as next_order FROM leagues")->fetch_assoc()['next_order'];
        $stmt->bind_param("ssssi", $_POST['name'], $_POST['country_code'], $_POST['display_name'], $_POST['category'], $sort_order);
        $stmt->execute();
    } elseif ($_POST['action'] == 'delete' && isset($_POST['id'])) {
        $stmt = $conn->prepare("DELETE FROM leagues WHERE id = ?");
        $stmt->bind_param("i", $_POST['id']);
        $stmt->execute();
    } elseif ($_POST['action'] == 'edit' && isset($_POST['id'])) {
        $stmt = $conn->prepare("UPDATE leagues SET name = ?, country_code = ?, display_name = ?, category = ? WHERE id = ?");
        $stmt->bind_param("ssssi", $_POST['name'], $_POST['country_code'], $_POST['display_name'], $_POST['category'], $_POST['id']);
        $stmt->execute();
    }
}

// Preluăm toate ligile
$leagues = $conn->query("SELECT * FROM leagues ORDER BY sort_order ASC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestionare Ligi - Admin Panel</title>
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
        .leagues-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        .leagues-table th,
        .leagues-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        .leagues-table th {
            background: rgba(0, 0, 0, 0.2);
            font-weight: 600;
        }
        .leagues-table tr:hover {
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
        input[type="text"],
        select {
            width: 100%;
            padding: 10px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 6px;
            background: rgba(0, 0, 0, 0.4);
            color: #fff;
            font-size: 14px;
        }
        .add-league-form {
            background: rgba(0, 0, 0, 0.2);
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
        }
        .league-flag {
            width: 24px;
            height: 18px;
            vertical-align: middle;
            margin-right: 8px;
            border-radius: 2px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Gestionare Ligi</h1>

        <div class="add-league-form">
            <h3>Adaugă Ligă Nouă</h3>
            <form method="post">
                <input type="hidden" name="action" value="add">
                <div class="form-group">
                    <label>Nume Ligă (cod intern):</label>
                    <input type="text" name="name" required placeholder="Ex: Premier League">
                </div>
                <div class="form-group">
                    <label>Cod Țară:</label>
                    <input type="text" name="country_code" required placeholder="Ex: gb-eng, es, it">
                </div>
                <div class="form-group">
                    <label>Nume Afișat:</label>
                    <input type="text" name="display_name" required placeholder="Ex: ANGLIA: Premier League">
                </div>
                <div class="form-group">
                    <label>Categorie:</label>
                    <select name="category" required>
                        <option value="European">Competiție Europeană</option>
                        <option value="National">Ligă Națională</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Adaugă Ligă</button>
            </form>
        </div>

        <table class="leagues-table">
            <thead>
                <tr>
                    <th>Steag</th>
                    <th>Nume Ligă</th>
                    <th>Nume Afișat</th>
                    <th>Categorie</th>
                    <th>Acțiuni</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($league = $leagues->fetch_assoc()): ?>
                <tr>
                    <td>
                        <img src="https://flagcdn.com/24x18/<?php echo htmlspecialchars($league['country_code']); ?>.png" 
                             alt="<?php echo htmlspecialchars($league['country_code']); ?>"
                             class="league-flag">
                    </td>
                    <td><?php echo htmlspecialchars($league['name']); ?></td>
                    <td><?php echo htmlspecialchars($league['display_name']); ?></td>
                    <td><?php echo htmlspecialchars($league['category']); ?></td>
                    <td>
                        <form method="post" style="display: inline;">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="id" value="<?php echo $league['id']; ?>">
                            <button type="submit" class="btn btn-danger" onclick="return confirm('Ești sigur că vrei să ștergi această ligă?')">Șterge</button>
                        </form>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
