<?php
session_start();
require_once '../inc/config.php';
require_once '../inc/db_config.php';
require_once 'admin_auth.php';

// Verifică autentificarea
requireAdminLogin();

// Get teams with their league names
$query = "SELECT t.*, l.name as league_name 
          FROM teams t 
          LEFT JOIN leagues l ON t.league_id = l.id 
          ORDER BY t.name ASC";
$result = $conn->query($query);

// Get statistics
$stats = [
    'total_teams' => 0,
    'total_leagues' => 0,
    'active_teams' => 0,
    'inactive_teams' => 0
];

// Get total teams
$query = "SELECT COUNT(*) as count FROM teams";
$count_result = mysqli_query($conn, $query);
if ($count_result) {
    $row = mysqli_fetch_assoc($count_result);
    $stats['total_teams'] = $row['count'];
}

// Get total leagues
$query = "SELECT COUNT(*) as count FROM leagues";
$count_result = mysqli_query($conn, $query);
if ($count_result) {
    $row = mysqli_fetch_assoc($count_result);
    $stats['total_leagues'] = $row['count'];
}

// Get active teams
$query = "SELECT COUNT(*) as count FROM teams WHERE status = 'active'";
$count_result = mysqli_query($conn, $query);
if ($count_result) {
    $row = mysqli_fetch_assoc($count_result);
    $stats['active_teams'] = $row['count'];
}

// Get inactive teams
$query = "SELECT COUNT(*) as count FROM teams WHERE status = 'inactive'";
$count_result = mysqli_query($conn, $query);
if ($count_result) {
    $row = mysqli_fetch_assoc($count_result);
    $stats['inactive_teams'] = $row['count'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Teams - <?php echo $site_title; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css/admin.css">
</head>
<body>
    <div class="admin-dashboard">
        <div class="sidebar">
            <div class="logo">
                <img src="../images/logo.png" alt="Logo">
                <span>Admin Panel</span>
            </div>
            <nav>
                <a href="index.php">
                    <i class="fas fa-home"></i>
                    <span>Dashboard</span>
                </a>
                <a href="matches.php">
                    <i class="fas fa-futbol"></i>
                    <span>Matches</span>
                </a>
                <a href="teams.php" class="active">
                    <i class="fas fa-users"></i>
                    <span>Teams</span>
                </a>
                <a href="leagues.php">
                    <i class="fas fa-trophy"></i>
                    <span>Leagues</span>
                </a>
                <a href="settings.php">
                    <i class="fas fa-cog"></i>
                    <span>Settings</span>
                </a>
                <a href="logout.php">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Logout</span>
                </a>
            </nav>
        </div>

        <div class="main-content">
            <header>
                <div class="search-bar">
                    <i class="fas fa-search"></i>
                    <input type="text" placeholder="Search teams...">
                </div>
                <div class="user-menu">
                    <span>Welcome, Admin</span>
                    <img src="../images/avatar.png" alt="Admin">
                </div>
            </header>

            <div class="content-header">
                <h1>Teams Management</h1>
                <button class="btn-primary" onclick="openModal('addTeamModal')">
                    <i class="fas fa-plus"></i> Add New Team
                </button>
            </div>

            <div class="content-body">
                <div class="data-table">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Logo</th>
                                <th>Team Name</th>
                                <th>League</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($result && $result->num_rows > 0): ?>
                                <?php while ($team = $result->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo $team['id']; ?></td>
                                        <td>
                                            <?php if (!empty($team['logo_url'])): ?>
                                                <img src="<?php echo htmlspecialchars($team['logo_url']); ?>" 
                                                     alt="<?php echo htmlspecialchars($team['name']); ?>" 
                                                     class="team-logo"
                                                     style="max-width: 40px; height: auto;">
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo htmlspecialchars($team['name']); ?></td>
                                        <td><?php echo htmlspecialchars($team['league_name']); ?></td>
                                        <td>
                                            <?php
                                            $status_class = $team['status'] === 'active' ? 'btn-success' : 'btn-danger';
                                            ?>
                                            <span class="btn <?php echo $status_class; ?>" style="font-size: 0.75rem; padding: 0.25rem 0.5rem;">
                                                <?php echo ucfirst($team['status']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <button class="btn-edit" onclick="editTeam(<?php echo $team['id']; ?>, '<?php echo addslashes($team['name']); ?>', '<?php echo addslashes($team['logo_url']); ?>')">
                                                <i class="fas fa-edit"></i> Edit
                                            </button>
                                            <button class="btn-delete" onclick="deleteItem(<?php echo $team['id']; ?>, 'team')">
                                                <i class="fas fa-trash"></i> Delete
                                            </button>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" style="text-align: center;">No teams found.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Team Modal -->
    <div id="addTeamModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Add New Team</h2>
                <span class="close" onclick="closeModal('addTeamModal')">&times;</span>
            </div>
            <form id="addTeamForm" onsubmit="event.preventDefault(); submitForm('addTeamForm', 'actions/add_team.php')">
                <div class="form-group">
                    <label>Team Name</label>
                    <input type="text" name="name" required>
                </div>
                <div class="form-group">
                    <label>Logo URL</label>
                    <input type="url" name="logo_url">
                </div>
                <button type="submit" class="btn-primary">Add Team</button>
            </form>
        </div>
    </div>

    <!-- Edit Team Modal -->
    <div id="editTeamModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Edit Team</h2>
                <span class="close" onclick="closeModal('editTeamModal')">&times;</span>
            </div>
            <form id="editTeamForm" onsubmit="event.preventDefault(); submitForm('editTeamForm', 'actions/edit_team.php')">
                <input type="hidden" id="edit_team_id" name="id">
                <div class="form-group">
                    <label>Team Name</label>
                    <input type="text" id="edit_team_name" name="name" required>
                </div>
                <div class="form-group">
                    <label>Logo URL</label>
                    <input type="url" id="edit_team_logo" name="logo_url">
                </div>
                <button type="submit" class="btn-primary">Update Team</button>
            </form>
        </div>
    </div>

    <script>
        function openModal(modalId) {
            document.getElementById(modalId).style.display = 'block';
        }

        function closeModal(modalId) {
            document.getElementById(modalId).style.display = 'none';
        }

        function editTeam(id, name, logoUrl) {
            document.getElementById('edit_team_id').value = id;
            document.getElementById('edit_team_name').value = name;
            document.getElementById('edit_team_logo').value = logoUrl;
            openModal('editTeamModal');
        }

        function deleteItem(id, type) {
            if (confirm('Are you sure you want to delete this ' + type + '?')) {
                // Send delete request to server
            }
        }

        function submitForm(formId, actionUrl) {
            const formData = new FormData(document.getElementById(formId));
            fetch(actionUrl, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => console.log(data))
            .catch(error => console.error(error));
        }
    </script>
</body>
</html>
