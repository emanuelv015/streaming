<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include auth first
require_once(__DIR__ . '/auth.php');
require_once(__DIR__ . '/../inc/db_config.php');

// Verifică dacă utilizatorul este logat
if (!isLoggedIn()) {
    header('Location: login.php');
    exit();
}

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Get the document root path
$root_path = $_SERVER['DOCUMENT_ROOT'];

$action = $_GET['action'] ?? 'list';
$error = '';
$success = '';

// Debug info
echo "<!-- Session ID: " . session_id() . " -->";
echo "<!-- Logged in: " . (isLoggedIn() ? 'Yes' : 'No') . " -->";

// La început, după require-uri
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Verifică dacă avem acces la baza de date
$test_query = "SELECT COUNT(*) as count FROM matches";
$test_result = $conn->query($test_query);
if ($test_result === false) {
    error_log("Cannot access matches table: " . $conn->error);
} else {
    $count = $test_result->fetch_assoc()['count'];
    error_log("Total matches in database: " . $count);
}

// Adaugă după require-uri, înainte de restul codului
function executeQuery($conn, $query, $params = [], $types = '') {
    $stmt = $conn->prepare($query);
    if ($stmt === false) {
        return false;
    }
    
    if (!empty($params)) {
        try {
            error_log("Attempting to bind parameters...");
            error_log("Types string: " . $types);
            error_log("Parameters to bind: " . print_r($params, true));
            
            if (!$stmt->bind_param($types, ...$params)) {
                error_log("Binding parameters failed: " . $stmt->error);
                return false;
            }
            error_log("Parameters bound successfully");
        } catch (Exception $e) {
            error_log("Exception during bind_param: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            return false;
        }
    }
    
    error_log("Executing query...");
    if (!$stmt->execute()) {
        error_log("Execute failed: " . $stmt->error);
        return false;
    }
    
    return $stmt->get_result();
}

// Adaugă această funcție la începutul fișierului, după require-uri
function updateMatchStatus($conn, $match_id, $new_status) {
    // Verifică dacă statusul s-a schimbat
    $check = $conn->prepare("SELECT status FROM matches WHERE id = ?");
    $check->bind_param("i", $match_id);
    $check->execute();
    $result = $check->get_result();
    $current = $result->fetch_assoc();

    if ($current['status'] !== $new_status) {
        $stmt = $conn->prepare("UPDATE matches SET status = ?, updated_at = NOW() WHERE id = ?");
        $stmt->bind_param("si", $new_status, $match_id);
        
        if ($stmt->execute()) {
            // Adaugă în istoric
            $history = $conn->prepare("INSERT INTO match_status_history (match_id, old_status, new_status) VALUES (?, ?, ?)");
            $history->bind_param("iss", $match_id, $current['status'], $new_status);
            $history->execute();
            
            return true;
        }
    }
    return false;
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($action === 'add' || $action === 'edit') {
        $home_team = $_POST['home_team'] ?? '';
        $away_team = $_POST['away_team'] ?? '';
        $league = $_POST['league'] ?? '';
        $date = $_POST['date'] ?? '';
        $time = $_POST['time'] ?? '';
        $status = $_POST['status'] ?? 'upcoming';
        $stream_url = $_POST['stream_url'] ?? '';
        $alternative_stream1 = $_POST['alternative_stream1'] ?? '';
        $alternative_stream2 = $_POST['alternative_stream2'] ?? '';
        $meta_title = $_POST['meta_title'] ?? '';
        $meta_description = $_POST['meta_description'] ?? '';
        $meta_keywords = $_POST['meta_keywords'] ?? '';

        $datetime = date('Y-m-d H:i:s', strtotime("$date $time"));

        // Get team names for slug
        $home_team_query = "SELECT name FROM teams WHERE id = ?";
        $away_team_query = "SELECT name FROM teams WHERE id = ?";
        
        // Get home team name
        $stmt = $conn->prepare($home_team_query);
        $stmt->bind_param("i", $home_team);
        $stmt->execute();
        $home_team_result = $stmt->get_result();
        $home_team_name = $home_team_result->fetch_assoc()['name'] ?? 'team';
        
        // Get away team name
        $stmt = $conn->prepare($away_team_query);
        $stmt->bind_param("i", $away_team);
        $stmt->execute();
        $away_team_result = $stmt->get_result();
        $away_team_name = $away_team_result->fetch_assoc()['name'] ?? 'team';
        
        // Generate slug
        $home_name = strtolower(preg_replace('/[^a-zA-Z0-9]+/', '-', $home_team_name));
        $away_name = strtolower(preg_replace('/[^a-zA-Z0-9]+/', '-', $away_team_name));
        $date_part = date('Y-m-d', strtotime($datetime));
        $slug = "{$home_name}-vs-{$away_name}-{$date_part}";

        // Verifică dacă slug-ul există deja și adaugă un sufix dacă e necesar
        $base_slug = $slug;
        $counter = 1;
        while (true) {
            $check_query = "SELECT id FROM matches WHERE slug = ? AND id != ?";
            $stmt = $conn->prepare($check_query);
            $id_to_check = $action === 'edit' ? $_POST['id'] : 0;
            $stmt->bind_param("si", $slug, $id_to_check);
            $stmt->execute();
            if ($stmt->get_result()->num_rows === 0) {
                break;
            }
            $slug = $base_slug . '-' . $counter;
            $counter++;
        }

        // Procesează robots meta
        $robots_meta = isset($_POST['robots_meta']) ? implode(',', $_POST['robots_meta']) : 'noindex,nofollow';
        $canonical_url = filter_var($_POST['canonical_url'] ?? '', FILTER_VALIDATE_URL) ?: null;

        if ($action === 'add') {
            $insert_query = "INSERT INTO matches (
                home_team, away_team, league, date, status, stream_url, 
                alternative_stream1, alternative_stream2, slug,
                meta_title, meta_description, meta_keywords
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

            $params = [
                $home_team, 
                $away_team, 
                $league, 
                $datetime, 
                $status, 
                $stream_url,
                $alternative_stream1, 
                $alternative_stream2, 
                $slug,
                $meta_title, 
                $meta_description, 
                $meta_keywords
            ];

            $result = executeQuery($conn, $insert_query, $params, 'iiisssssssss');

            if ($result !== false) {
                $success = "Meciul a fost adăugat cu succes!";
                header("Location: matches.php");
                exit();
            } else {
                $error = "A apărut o eroare. Verifică datele și încearcă din nou.";
            }
        } else {
            // Edit action
            $id = $_POST['id'] ?? $_GET['id'] ?? '';
            
            if (!$id) {
                $error = "ID-ul meciului lipsește!";
            } else {
                $update_query = "UPDATE matches SET 
                    home_team = ?, 
                    away_team = ?, 
                    league = ?, 
                    date = ?, 
                    status = ?, 
                    stream_url = ?,
                    alternative_stream1 = ?, 
                    alternative_stream2 = ?, 
                    slug = ?,
                    meta_title = ?, 
                    meta_description = ?, 
                    meta_keywords = ?,
                    meta_title_ro = ?,
                    meta_description_ro = ?,
                    meta_keywords_ro = ?,
                    meta_title_en = ?,
                    meta_description_en = ?,
                    meta_keywords_en = ?,
                    meta_title_fr = ?,
                    meta_description_fr = ?,
                    meta_keywords_fr = ?,
                    canonical_url = ?,
                    robots_meta = ?
                WHERE id = ?";

                $params = [
                    $home_team, 
                    $away_team, 
                    $league, 
                    $datetime, 
                    $status, 
                    $stream_url,
                    $alternative_stream1, 
                    $alternative_stream2, 
                    $slug,
                    $meta_title, 
                    $meta_description, 
                    $meta_keywords,
                    $_POST['meta_title_ro'],
                    $_POST['meta_description_ro'],
                    $_POST['meta_keywords_ro'],
                    $_POST['meta_title_en'],
                    $_POST['meta_description_en'],
                    $_POST['meta_keywords_en'],
                    $_POST['meta_title_fr'],
                    $_POST['meta_description_fr'],
                    $_POST['meta_keywords_fr'],
                    $canonical_url,
                    implode(',', $_POST['robots_meta'] ?? ['index', 'follow']),
                    $id
                ];

                $types = 'iiissssssssssssssssssssi';
                $result = executeQuery($conn, $update_query, $params, $types);
                
                if ($result !== false) {
                    $success = "Meciul a fost actualizat cu succes!";
                    header("Location: matches.php");
                    exit();
                }
            }
        }
    } elseif ($action === 'delete') {
        $id = $_POST['id'] ?? '';
        $stmt = $conn->prepare("DELETE FROM matches WHERE id=?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $success = 'Match deleted successfully!';
        $action = 'list';
    } elseif (isset($_POST['update_status'])) {
        $match_id = $_POST['match_id'];
        $new_status = $_POST['status'];
        
        if (updateMatchStatus($conn, $match_id, $new_status)) {
            $_SESSION['success'] = "Match status updated successfully!";
        } else {
            $_SESSION['error'] = "Error updating match status or status unchanged";
        }
        
        // Refresh pagina după update
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }
}

// Get match data for editing
$editMatch = null;
if ($action === 'edit') {
    $id = $_GET['id'] ?? '';
    
    $query = "SELECT m.*, 
            h.name as home_team_name,
            a.name as away_team_name,
            l.name as league_name,
            h.logo_url as home_team_logo,
            a.logo_url as away_team_logo,
            l.logo_url as league_logo
     FROM matches m
     LEFT JOIN teams h ON m.home_team = h.id
     LEFT JOIN teams a ON m.away_team = a.id
     LEFT JOIN leagues l ON m.league = l.id
     WHERE m.id = ?";

    $result = executeQuery($conn, $query, [$id], 'i');
    
    if ($result === false) {
        $error = "Eroare la interogarea bazei de date";
    } else {
        $editMatch = $result->fetch_assoc();
        if (!$editMatch) {
            $error = "Nu s-a putut găsi meciul cu ID-ul: " . $id;
        }
    }
}

// Get all leagues and teams for dropdowns
$leagues = [];
$result = executeQuery($conn, "SELECT id, name FROM leagues ORDER BY name ASC");
$leagues = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];

$teams = [];
$result = executeQuery($conn, "SELECT id, name FROM teams ORDER BY name ASC");
$teams = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];

// Get matches list
$query = "
    SELECT m.*, 
           h.name as home_team_name, 
           a.name as away_team_name,
           h.logo_url as home_team_logo,
           a.logo_url as away_team_logo,
           l.name as league_name,
           l.logo_url as league_logo
    FROM matches m 
    LEFT JOIN teams h ON m.home_team = h.id 
    LEFT JOIN teams a ON m.away_team = a.id 
    LEFT JOIN leagues l ON m.league = l.id 
    ORDER BY 
        CASE m.status
            WHEN 'live' THEN 0     -- Meciurile live primele
            WHEN 'upcoming' THEN 1  -- Apoi cele upcoming
            WHEN 'ended' THEN 2     -- Ended la final
            ELSE 1                  -- Alte statusuri după upcoming
        END ASC,
        m.date DESC";

$result = $conn->query($query);
$matches = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Matches - GetSportNews</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .sidebar {
            min-height: 100vh;
            background: #343a40;
            padding-top: 20px;
        }
        .sidebar a {
            color: #fff;
            text-decoration: none;
            padding: 10px 15px;
            display: block;
        }
        .sidebar a:hover {
            background: #495057;
        }
        .content {
            padding: 20px;
        }
        .nav-link.active {
            background: #495057;
        }
        .is-invalid {
            border-color: #dc3545;
        }
        #schemaPreview {
            max-height: 200px;
            overflow-y: auto;
            font-size: 0.8rem;
        }
        .input-group-text {
            font-size: 0.8rem;
            min-width: 60px;
            justify-content: center;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-2 sidebar">
                <h4 class="text-white text-center mb-4">Admin Panel</h4>
                <nav>
                    <a href="index.php" class="nav-link">
                        <i class="bi bi-house-door"></i> Dashboard
                    </a>
                    <a href="matches.php" class="nav-link active">
                        <i class="bi bi-controller"></i> Matches
                    </a>
                    <a href="leagues.php" class="nav-link">
                        <i class="bi bi-trophy"></i> Leagues
                    </a>
                    <a href="teams.php" class="nav-link">
                        <i class="bi bi-people"></i> Teams
                    </a>
                    <a href="logout.php" class="nav-link text-danger mt-5">
                        <i class="bi bi-box-arrow-right"></i> Logout
                    </a>
                </nav>
            </div>

            <!-- Main Content -->
            <div class="col-md-10 content">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2>Manage Matches</h2>
                    <?php if ($action === 'list'): ?>
                        <a href="?action=add" class="btn btn-primary">
                            <i class="bi bi-plus-circle"></i> Add New Match
                        </a>
                    <?php endif; ?>
                </div>

                <!-- Adaugă acest buton în partea de sus a paginii -->
                <div class="bulk-actions mb-4">
                    <button id="deleteAllMatches" class="btn btn-danger">
                        <i class="fas fa-trash"></i> Delete All Finished Matches
                    </button>
                </div>

                <?php if ($error): ?>
                    <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>

                <?php if ($success): ?>
                    <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
                <?php endif; ?>

                <?php if ($action === 'list'): ?>
                    <!-- Matches List -->
                    <div class="card">
                        <div class="card-body">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Home Team</th>
                                        <th>Away Team</th>
                                        <th>League</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($matches)): ?>
                                        <tr>
                                            <td colspan="6" class="text-center">No matches found</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($matches as $match): ?>
                                            <tr>
                                                <td><?php echo date('Y-m-d H:i', strtotime($match['date'])); ?></td>
                                                <td>
                                                    <img src="<?php echo htmlspecialchars($match['home_team_logo']); ?>" 
                                                         alt="<?php echo htmlspecialchars($match['home_team_name']); ?>" 
                                                         class="team-logo" style="width: 25px; height: 25px;">
                                                    <?php echo htmlspecialchars($match['home_team_name']); ?>
                                                </td>
                                                <td>
                                                    <img src="<?php echo htmlspecialchars($match['away_team_logo']); ?>" 
                                                         alt="<?php echo htmlspecialchars($match['away_team_name']); ?>" 
                                                         class="team-logo" style="width: 25px; height: 25px;">
                                                    <?php echo htmlspecialchars($match['away_team_name']); ?>
                                                </td>
                                                <td>
                                                    <img src="<?php echo htmlspecialchars($match['league_logo']); ?>" 
                                                         alt="<?php echo htmlspecialchars($match['league_name']); ?>" 
                                                         class="league-logo" style="width: 25px; height: 25px;">
                                                    <?php echo htmlspecialchars($match['league_name']); ?>
                                                </td>
                                                <td>
                                                    <form method="POST" action="" style="display: inline;">
                                                        <input type="hidden" name="match_id" value="<?php echo $match['id']; ?>">
                                                        <select name="status" onchange="this.form.submit()" class="form-select form-select-sm" style="width: auto;">
                                                            <option value="upcoming" <?php echo $match['status'] == 'upcoming' ? 'selected' : ''; ?>>Upcoming</option>
                                                            <option value="live" <?php echo $match['status'] == 'live' ? 'selected' : ''; ?>>Live</option>
                                                            <option value="ended" <?php echo $match['status'] == 'ended' ? 'selected' : ''; ?>>Ended</option>
                                                            <option value="cancelled" <?php echo $match['status'] == 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                                        </select>
                                                        <input type="hidden" name="update_status" value="1">
                                                    </form>
                                                </td>
                                                <td>
                                                    <a href="?action=edit&id=<?php echo $match['id']; ?>" class="btn btn-sm btn-primary">
                                                        <i class="bi bi-pencil"></i> Edit
                                                    </a>
                                                    <form method="POST" action="?action=delete" class="d-inline" 
                                                          onsubmit="return confirm('Are you sure you want to delete this match?');">
                                                        <input type="hidden" name="id" value="<?php echo $match['id']; ?>">
                                                        <button type="submit" class="btn btn-sm btn-danger">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                <?php else: ?>
                    <!-- Add/Edit Match Form -->
                    <div class="card">
                        <div class="card-body">
                            <form method="POST" action="?action=<?php echo $action . ($editMatch ? '&id=' . $editMatch['id'] : ''); ?>">
                                <?php if ($editMatch): ?>
                                    <input type="hidden" name="id" value="<?php echo $editMatch['id']; ?>">
                                <?php endif; ?>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Home Team</label>
                                            <select name="home_team" class="form-control" required>
                                                <option value="">Select Home Team</option>
                                                <?php foreach ($teams as $team): ?>
                                                    <option value="<?php echo $team['id']; ?>" 
                                                        <?php echo ($editMatch && $editMatch['home_team'] == $team['id']) ? 'selected' : ''; ?>>
                                                        <?php echo htmlspecialchars($team['name']); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Away Team</label>
                                            <select name="away_team" class="form-control" required>
                                                <option value="">Select Away Team</option>
                                                <?php foreach ($teams as $team): ?>
                                                    <option value="<?php echo $team['id']; ?>" 
                                                        <?php echo ($editMatch && $editMatch['away_team'] == $team['id']) ? 'selected' : ''; ?>>
                                                        <?php echo htmlspecialchars($team['name']); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">League</label>
                                            <select name="league" class="form-control" required>
                                                <option value="">Select League</option>
                                                <?php foreach ($leagues as $league): ?>
                                                    <option value="<?php echo $league['id']; ?>" 
                                                        <?php echo ($editMatch && $editMatch['league'] == $league['id']) ? 'selected' : ''; ?>>
                                                        <?php echo htmlspecialchars($league['name']); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="mb-3">
                                            <label class="form-label">Date</label>
                                            <input type="date" name="date" class="form-control" required
                                                value="<?php echo $editMatch ? date('Y-m-d', strtotime($editMatch['date'])) : date('Y-m-d'); ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="mb-3">
                                            <label class="form-label">Time</label>
                                            <input type="time" name="time" class="form-control" required
                                                value="<?php echo $editMatch ? date('H:i', strtotime($editMatch['date'])) : ''; ?>">
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Status</label>
                                    <select name="status" class="form-control" required>
                                        <option value="upcoming" <?php echo ($editMatch && $editMatch['status'] == 'upcoming') ? 'selected' : ''; ?>>Upcoming</option>
                                        <option value="live" <?php echo ($editMatch && $editMatch['status'] == 'live') ? 'selected' : ''; ?>>Live</option>
                                        <option value="ended" <?php echo ($editMatch && $editMatch['status'] == 'ended') ? 'selected' : ''; ?>>Ended</option>
                                        <option value="pending" <?php echo ($editMatch && $editMatch['status'] == 'pending') ? 'selected' : ''; ?>>Pending</option>
                                        <option value="postponed" <?php echo ($editMatch && $editMatch['status'] == 'postponed') ? 'selected' : ''; ?>>Postponed</option>
                                        <option value="cancelled" <?php echo ($editMatch && $editMatch['status'] == 'cancelled') ? 'selected' : ''; ?>>Cancelled</option>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Stream URL</label>
                                    <input type="url" name="stream_url" class="form-control"
                                        value="<?php echo $editMatch ? htmlspecialchars($editMatch['stream_url']) : ''; ?>">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Alternative Stream 1</label>
                                    <input type="url" name="alternative_stream1" class="form-control"
                                        value="<?php echo $editMatch ? htmlspecialchars($editMatch['alternative_stream1']) : ''; ?>">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Alternative Stream 2</label>
                                    <input type="url" name="alternative_stream2" class="form-control"
                                        value="<?php echo $editMatch ? htmlspecialchars($editMatch['alternative_stream2']) : ''; ?>">
                                </div>

                                <div class="card mb-4">
                                    <div class="card-header d-flex justify-content-between align-items-center">
                                        <h5 class="mb-0">Setări SEO</h5>
                                        <div>
                                            <button type="button" class="btn btn-sm btn-outline-info me-2" data-bs-toggle="modal" data-bs-target="#seoHelpModal">
                                                <i class="bi bi-question-circle"></i> Ghid SEO
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-primary" id="generateSeoBtn">
                                                <i class="bi bi-magic"></i> Generează SEO
                                            </button>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <!-- Meta Title -->
                                        <div class="mb-3">
                                            <label for="meta_title" class="form-label">Titlu Meta (SEO)</label>
                                            <div class="input-group">
                                                <input type="text" class="form-control" id="meta_title" name="meta_title" 
                                                       value="<?php echo htmlspecialchars($editMatch['meta_title'] ?? ''); ?>"
                                                       maxlength="60">
                                                <span class="input-group-text">
                                                    <span id="meta_title_counter" class="small">0/60</span>
                                                </span>
                                            </div>
                                            <div class="form-text">
                                                Titlul care apare în rezultatele Google. Format recomandat: "Echipa1 vs Echipa2 - Live Stream | Liga"
                                            </div>
                                        </div>

                                        <!-- Meta Description -->
                                        <div class="mb-3">
                                            <label for="meta_description" class="form-label">Descriere Meta</label>
                                            <div class="input-group">
                                                <textarea class="form-control" id="meta_description" name="meta_description" 
                                                          rows="3" maxlength="160"><?php echo htmlspecialchars($editMatch['meta_description'] ?? ''); ?></textarea>
                                                <span class="input-group-text">
                                                    <span id="meta_description_counter" class="small">0/160</span>
                                                </span>
                                            </div>
                                            <div class="form-text">
                                                Descrierea care apare sub titlu în Google. Include detalii despre meci, echipe și ligă.
                                            </div>
                                        </div>

                                        <!-- Meta Keywords -->
                                        <div class="mb-3">
                                            <label for="meta_keywords" class="form-label">Cuvinte Cheie</label>
                                            <input type="text" class="form-control" id="meta_keywords" name="meta_keywords" 
                                                   value="<?php echo htmlspecialchars($editMatch['meta_keywords'] ?? ''); ?>">
                                            <div class="form-text">
                                                Separă cuvintele cheie prin virgulă (ex: fotbal live, stream fotbal, liga 1)
                                            </div>
                                        </div>

                                        <!-- URL Canonical -->
                                        <div class="mb-3">
                                            <label for="canonical_url" class="form-label">URL Canonical</label>
                                            <input type="url" class="form-control" id="canonical_url" name="canonical_url"
                                                   value="<?php echo htmlspecialchars($editMatch['canonical_url'] ?? ''); ?>">
                                            <div class="form-text">
                                                <i class="bi bi-info-circle"></i> URL-ul canonical ajută la evitarea conținutului duplicat. 
                                                Lasă gol pentru a folosi URL-ul implicit al meciului.
                                            </div>
                                        </div>

                                        <!-- Robots Meta -->
                                        <div class="mb-3">
                                            <label class="form-label">Indexare și Follow</label>
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input" id="robots_index" name="robots_meta[]" 
                                                       value="index" <?php echo (!$editMatch || strpos($editMatch['robots_meta'] ?? '', 'index') !== false) ? 'checked' : ''; ?>>
                                                <label class="form-check-label" for="robots_index">Permite indexarea (index)</label>
                                            </div>
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input" id="robots_follow" name="robots_meta[]" 
                                                       value="follow" <?php echo (!$editMatch || strpos($editMatch['robots_meta'] ?? '', 'follow') !== false) ? 'checked' : ''; ?>>
                                                <label class="form-check-label" for="robots_follow">Permite urmărirea link-urilor (follow)</label>
                                            </div>
                                        </div>

                                        <!-- Social Media Preview -->
                                        <div class="mb-3">
                                            <label class="form-label">Previzualizare Social Media</label>
                                            <div class="card">
                                                <div class="card-body bg-light">
                                                    <div id="socialPreview" class="border p-3 rounded">
                                                        <h5 class="preview-title"></h5>
                                                        <p class="preview-url text-success small"></p>
                                                        <p class="preview-description text-muted"></p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="card mb-4">
                                    <div class="card-header d-flex justify-content-between align-items-center">
                                        <h5>SEO pentru Multiple Limbi</h5>
                                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="generateSeoData()">
                                            <i class="bi bi-magic"></i> Generează pentru toate limbile
                                        </button>
                                    </div>
                                    <div class="card-body">
                                        <!-- Romanian -->
                                        <div class="mb-4">
                                            <h6 class="border-bottom pb-2">Română</h6>
                                            <div class="mb-3">
                                                <label class="form-label">Titlu RO</label>
                                                <div class="input-group">
                                                    <input type="text" class="form-control" name="meta_title_ro" 
                                                           value="<?php echo htmlspecialchars($editMatch['meta_title_ro'] ?? ''); ?>"
                                                           placeholder="Ex: Steaua vs Dinamo - Meci Live Stream | Liga 1">
                                                    <button type="button" class="btn btn-outline-secondary" onclick="generateSeoData('ro')">
                                                        <i class="bi bi-translate"></i> RO
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Descriere RO</label>
                                                <textarea class="form-control" name="meta_description_ro" rows="2"><?php echo htmlspecialchars($editMatch['meta_description_ro'] ?? ''); ?></textarea>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Cuvinte Cheie RO</label>
                                                <input type="text" class="form-control" name="meta_keywords_ro" 
                                                       value="<?php echo htmlspecialchars($editMatch['meta_keywords_ro'] ?? ''); ?>">
                                            </div>
                                        </div>

                                        <!-- English -->
                                        <div class="mb-4">
                                            <h6 class="border-bottom pb-2">English</h6>
                                            <div class="mb-3">
                                                <label class="form-label">Titlu EN</label>
                                                <div class="input-group">
                                                    <input type="text" class="form-control" name="meta_title_en"
                                                           value="<?php echo htmlspecialchars($editMatch['meta_title_en'] ?? ''); ?>"
                                                           placeholder="Ex: Steaua vs Dinamo - Live Stream | Liga 1">
                                                    <button type="button" class="btn btn-outline-secondary" onclick="generateSeoData('en')">
                                                        <i class="bi bi-translate"></i> EN
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Descriere EN</label>
                                                <textarea class="form-control" name="meta_description_en" rows="2"><?php echo htmlspecialchars($editMatch['meta_description_en'] ?? ''); ?></textarea>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Cuvinte Cheie EN</label>
                                                <input type="text" class="form-control" name="meta_keywords_en" 
                                                       value="<?php echo htmlspecialchars($editMatch['meta_keywords_en'] ?? ''); ?>">
                                            </div>
                                        </div>

                                        <!-- French -->
                                        <div class="mb-4">
                                            <h6 class="border-bottom pb-2">Français</h6>
                                            <div class="mb-3">
                                                <label class="form-label">Titre FR</label>
                                                <div class="input-group">
                                                    <input type="text" class="form-control" name="meta_title_fr"
                                                           value="<?php echo htmlspecialchars($editMatch['meta_title_fr'] ?? ''); ?>"
                                                           placeholder="Ex: Steaua vs Dinamo - Stream en Direct | Liga 1">
                                                    <button type="button" class="btn btn-outline-secondary" onclick="generateSeoData('fr')">
                                                        <i class="bi bi-translate"></i> FR
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Descriere FR</label>
                                                <textarea class="form-control" name="meta_description_fr" rows="2"><?php echo htmlspecialchars($editMatch['meta_description_fr'] ?? ''); ?></textarea>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Cuvinte Cheie FR</label>
                                                <input type="text" class="form-control" name="meta_keywords_fr" 
                                                       value="<?php echo htmlspecialchars($editMatch['meta_keywords_fr'] ?? ''); ?>">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <button type="submit" class="btn btn-primary">
                                        <?php echo $action === 'add' ? 'Add Match' : 'Update Match'; ?>
                                    </button>
                                    <a href="matches.php" class="btn btn-secondary">Cancel</a>
                                </div>
                            </form>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Modal Ghid SEO -->
    <div class="modal fade" id="seoHelpModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Ghid Optimizare SEO</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <h6>Ce este SEO?</h6>
                    <p>SEO (Search Engine Optimization) ajută pagina ta să apară mai sus în rezultatele Google.</p>
                    
                    <h6>Cum să optimizezi:</h6>
                    <ul>
                        <li><strong>Titlu Meta:</strong> Include numele echipelor și competiția (max 60 caractere)</li>
                        <li><strong>Descriere Meta:</strong> Descrie meciul și include cuvinte cheie naturale (max 160 caractere)</li>
                        <li><strong>URL Canonical:</strong> Previne penalizările pentru conținut duplicat</li>
                        <li><strong>Cuvinte Cheie:</strong> Include termeni relevanți pentru meci și streaming</li>
                    </ul>

                    <h6>Cele mai bune practici:</h6>
                    <ul>
                        <li>Folosește titluri descriptive și atractive</li>
                        <li>Include data și ora meciului în descriere</li>
                        <li>Menționează liga sau competiția</li>
                        <li>Adaugă cuvinte cheie relevante dar naturale</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    // Funcție pentru validarea și actualizarea contoarelor
    function updateCounter(element, maxLength) {
        const length = element.value.length;
        const counter = document.getElementById(`${element.id}_counter`);
        counter.textContent = `${length}/${maxLength}`;
        
        if (length > maxLength) {
            counter.classList.add('text-danger');
            element.classList.add('is-invalid');
        } else {
            counter.classList.remove('text-danger');
            element.classList.remove('is-invalid');
        }
    }

    // Modifică funcția generateSeoData pentru a include toate limbile
    function generateSeoData() {
        const homeTeam = document.querySelector('[name="home_team"] option:checked').text;
        const awayTeam = document.querySelector('[name="away_team"] option:checked').text;
        const league = document.querySelector('[name="league"] option:checked').text;
        const date = document.querySelector('[name="date"]').value;
        const formattedDate = new Date(date).toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });

        // Titluri în diferite limbi
        const titles = {
            'ro': `${homeTeam} vs ${awayTeam} - Meci Live Stream | ${league}`,
            'en': `${homeTeam} vs ${awayTeam} - Live Stream | ${league}`,
            'fr': `${homeTeam} vs ${awayTeam} - Match en Direct | ${league}`,
            'es': `${homeTeam} vs ${awayTeam} - Partido en Vivo | ${league}`
        };

        // Descrieri în diferite limbi
        const descriptions = {
            'ro': `Urmărește ${homeTeam} vs ${awayTeam} live stream. Meci din ${league} pe ${formattedDate}. Transmisiune fotbal de calitate HD, scoruri live și comentarii.`,
            'en': `Watch ${homeTeam} vs ${awayTeam} live stream. ${league} match on ${formattedDate}. High quality football streaming with live scores and updates.`,
            'fr': `Regardez ${homeTeam} vs ${awayTeam} en direct. Match de ${league} le ${formattedDate}. Streaming football haute qualité avec scores en direct.`,
            'es': `Ver ${homeTeam} vs ${awayTeam} en vivo. Partido de ${league} el ${formattedDate}. Transmisión de fútbol en alta calidad con resultados en directo.`
        };

        // Keywords în diferite limbi
        const keywords = {
            'ro': `${homeTeam}, ${awayTeam}, ${league}, fotbal live, stream fotbal, meciuri online, transmisiune directă`,
            'en': `${homeTeam}, ${awayTeam}, ${league}, live football, soccer stream, watch online, live match`,
            'fr': `${homeTeam}, ${awayTeam}, ${league}, football en direct, streaming foot, regarder match, direct`,
            'es': `${homeTeam}, ${awayTeam}, ${league}, fútbol en vivo, ver partido, transmisión directa, en directo`
        };

        // Setează valorile pentru toate limbile
        document.querySelector('[name="meta_title_ro"]').value = titles.ro;
        document.querySelector('[name="meta_title_en"]').value = titles.en;
        document.querySelector('[name="meta_title_fr"]').value = titles.fr;

        // Setează și valorile pentru câmpurile principale SEO
        document.getElementById('meta_title').value = titles.en; // folosim engleza ca default
        document.getElementById('meta_description').value = descriptions.en;
        document.getElementById('meta_keywords').value = keywords.en;

        // Update counters
        updateCounter(document.getElementById('meta_title'), 60);
        updateCounter(document.getElementById('meta_description'), 160);

        // Update preview
        updateSocialPreview();
    }

    // Funcție pentru actualizarea preview-ului Schema.org
    function updateSchemaPreview() {
        const homeTeam = document.querySelector('[name="home_team"] option:checked').text;
        const awayTeam = document.querySelector('[name="away_team"] option:checked').text;
        const league = document.querySelector('[name="league"] option:checked').text;
        const date = document.querySelector('[name="date"]').value;
        const time = document.querySelector('[name="time"]').value;

        const schema = {
            "@context": "https://schema.org",
            "@type": "SportsEvent",
            "name": `${homeTeam} vs ${awayTeam}`,
            "startDate": `${date}T${time}`,
            "sport": "Soccer",
            "competitor": [
                {
                    "@type": "SportsTeam",
                    "name": homeTeam
                },
                {
                    "@type": "SportsTeam",
                    "name": awayTeam
                }
            ],
            "organizer": {
                "@type": "Organization",
                "name": league
            }
        };

        document.getElementById('schemaPreview').textContent = 
            JSON.stringify(schema, null, 2);
    }

    // Event Listeners
    document.getElementById('meta_title').addEventListener('input', function() {
        updateCounter(this, 60);
    });

    document.getElementById('meta_description').addEventListener('input', function() {
        updateCounter(this, 160);
    });

    document.getElementById('generateSeoBtn').addEventListener('click', generateSeoData);

    // Actualizează SEO data când se schimbă echipele sau liga
    ['home_team', 'away_team', 'league', 'date', 'time'].forEach(field => {
        document.querySelector(`[name="${field}"]`).addEventListener('change', updateSchemaPreview);
    });

    // Inițializare la încărcarea paginii
    document.addEventListener('DOMContentLoaded', function() {
        updateCounter(document.getElementById('meta_title'), 60);
        updateCounter(document.getElementById('meta_description'), 160);
        updateSchemaPreview();
    });

    document.getElementById('deleteAllMatches').addEventListener('click', function() {
        if (confirm('Are you sure you want to delete all finished matches? This action cannot be undone.')) {
            fetch('ajax/delete_all_matches.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('All finished matches have been deleted successfully!');
                    location.reload();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while deleting matches.');
            });
        }
    });
    </script>
</body>
</html>
