<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Get the document root path
$root_path = $_SERVER['DOCUMENT_ROOT'];
require_once($root_path . '/admin/auth.php');
requireLogin();

$action = $_GET['action'] ?? 'list';
$error = '';
$success = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($action === 'add' || $action === 'edit') {
        $name = $_POST['name'] ?? '';
        $league_id = $_POST['league_id'] ?? '';
        $logo_url = $_POST['logo_url'] ?? '';
        $status = $_POST['status'] ?? 'active';
        
        // Create slug from name
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name)));

        if ($action === 'add') {
            $stmt = $conn->prepare("INSERT INTO teams (name, slug, league_id, logo_url, status) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("ssiss", $name, $slug, $league_id, $logo_url, $status);
            $stmt->execute();
            $success = 'Team added successfully!';
        } else {
            $id = $_POST['id'] ?? '';
            $stmt = $conn->prepare("UPDATE teams SET name=?, slug=?, league_id=?, logo_url=?, status=? WHERE id=?");
            $stmt->bind_param("ssissi", $name, $slug, $league_id, $logo_url, $status, $id);
            $stmt->execute();
            $success = 'Team updated successfully!';
        }
        $action = 'list';
    } elseif ($action === 'delete') {
        $id = $_POST['id'] ?? '';
        $stmt = $conn->prepare("DELETE FROM teams WHERE id=?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $success = 'Team deleted successfully!';
        $action = 'list';
    }
}

// Get team data for editing
$editTeam = null;
if ($action === 'edit') {
    $id = $_GET['id'] ?? '';
    $stmt = $conn->prepare("SELECT * FROM teams WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $editTeam = $result->fetch_assoc();
}

// Get all leagues for dropdown
$result = $conn->query("SELECT * FROM leagues ORDER BY name");
$leagues = $result->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Teams - Admin Panel</title>
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
        .team-logo {
            width: 50px;
            height: 50px;
            object-fit: contain;
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
                    <a href="matches.php" class="nav-link">
                        <i class="bi bi-controller"></i> Matches
                    </a>
                    <a href="leagues.php" class="nav-link">
                        <i class="bi bi-trophy"></i> Leagues
                    </a>
                    <a href="teams.php" class="nav-link active">
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
                    <h2>Manage Teams</h2>
                    <?php if ($action === 'list'): ?>
                        <a href="?action=add" class="btn btn-primary">
                            <i class="bi bi-plus-circle"></i> Add New Team
                        </a>
                    <?php endif; ?>
                </div>

                <?php if ($error): ?>
                    <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>

                <?php if ($success): ?>
                    <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
                <?php endif; ?>

                <?php if ($action === 'list'): ?>
                    <!-- Teams List -->
                    <div class="card">
                        <div class="card-body">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Logo</th>
                                        <th>Name</th>
                                        <th>League</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $result = $conn->query("
                                        SELECT t.*, l.name as league_name 
                                        FROM teams t 
                                        LEFT JOIN leagues l ON t.league_id = l.id 
                                        ORDER BY t.name
                                    ");
                                    while ($team = $result->fetch_assoc()): ?>
                                        <tr>
                                            <td>
                                                <?php if ($team['logo_url']): ?>
                                                    <img src="<?php echo htmlspecialchars($team['logo_url']); ?>" alt="<?php echo htmlspecialchars($team['name']); ?>" class="team-logo">
                                                <?php else: ?>
                                                    <div class="team-logo bg-light d-flex align-items-center justify-content-center">
                                                        <i class="bi bi-people text-muted"></i>
                                                    </div>
                                                <?php endif; ?>
                                            </td>
                                            <td><?php echo htmlspecialchars($team['name']); ?></td>
                                            <td><?php echo htmlspecialchars($team['league_name'] ?? 'No League'); ?></td>
                                            <td>
                                                <span class="badge bg-<?php echo $team['status'] === 'active' ? 'success' : 'secondary'; ?>">
                                                    <?php echo ucfirst($team['status']); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <a href="?action=edit&id=<?php echo $team['id']; ?>" class="btn btn-sm btn-primary">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <form method="POST" action="?action=delete" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this team?');">
                                                    <input type="hidden" name="id" value="<?php echo $team['id']; ?>">
                                                    <button type="submit" class="btn btn-sm btn-danger">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                <?php else: ?>
                    <!-- Add/Edit Team Form -->
                    <div class="card">
                        <div class="card-body">
                            <form method="POST" action="?action=<?php echo $action; ?>">
                                <?php if ($editTeam): ?>
                                    <input type="hidden" name="id" value="<?php echo $editTeam['id']; ?>">
                                <?php endif; ?>

                                <div class="mb-3">
                                    <label class="form-label">Team Name</label>
                                    <input type="text" name="name" class="form-control" required
                                        value="<?php echo $editTeam ? htmlspecialchars($editTeam['name']) : ''; ?>">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">League</label>
                                    <select name="league_id" class="form-control">
                                        <option value="">Select League</option>
                                        <?php foreach ($leagues as $league): ?>
                                            <option value="<?php echo $league['id']; ?>" <?php echo ($editTeam && $editTeam['league_id'] == $league['id']) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($league['name']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Logo URL</label>
                                    <input type="url" name="logo_url" class="form-control"
                                        value="<?php echo $editTeam ? htmlspecialchars($editTeam['logo_url']) : ''; ?>">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Status</label>
                                    <select name="status" class="form-control" required>
                                        <option value="active" <?php echo ($editTeam && $editTeam['status'] == 'active') ? 'selected' : ''; ?>>Active</option>
                                        <option value="inactive" <?php echo ($editTeam && $editTeam['status'] == 'inactive') ? 'selected' : ''; ?>>Inactive</option>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <button type="submit" class="btn btn-primary">
                                        <?php echo $action === 'add' ? 'Add Team' : 'Update Team'; ?>
                                    </button>
                                    <a href="teams.php" class="btn btn-secondary">Cancel</a>
                                </div>
                            </form>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
