<?php
session_start();
require_once '../inc/config.php';
require_once '../inc/db_config.php';
require_once 'admin_auth.php';

// Verifică autentificarea
requireAdminLogin();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Leagues - <?php echo $site_title; ?></title>
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
                <a href="teams.php">
                    <i class="fas fa-users"></i>
                    <span>Teams</span>
                </a>
                <a href="leagues.php" class="active">
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
                    <input type="text" placeholder="Search leagues...">
                </div>
                <div class="user-menu">
                    <span>Welcome, Admin</span>
                    <img src="../images/avatar.png" alt="Admin">
                </div>
            </header>

            <?php
            require_once 'header.php';
            ?>

            <div class="content-header">
                <h1>Leagues Management</h1>
                <button class="btn-primary" onclick="openModal('addLeagueModal')">
                    <i class="fas fa-plus"></i> Add New League
                </button>
            </div>

            <div class="content-body">
                <div class="data-table">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Flag</th>
                                <th>League Name</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $query = "SELECT * FROM leagues ORDER BY name ASC";
                            $result = $conn->query($query);

                            while ($league = $result->fetch_assoc()):
                            ?>
                            <tr>
                                <td><?php echo $league['id']; ?></td>
                                <td>
                                    <?php if (!empty($league['flag_url'])): ?>
                                        <img src="<?php echo htmlspecialchars($league['flag_url']); ?>" 
                                             alt="<?php echo htmlspecialchars($league['name']); ?>" 
                                             class="league-flag"
                                             style="max-width: 40px; height: auto;">
                                    <?php endif; ?>
                                </td>
                                <td><?php echo htmlspecialchars($league['name']); ?></td>
                                <td>
                                    <button class="btn-edit" onclick="editLeague(<?php echo $league['id']; ?>, '<?php echo addslashes($league['name']); ?>', '<?php echo addslashes($league['flag_url']); ?>')">
                                        <i class="fas fa-edit"></i> Edit
                                    </button>
                                    <button class="btn-delete" onclick="deleteItem(<?php echo $league['id']; ?>, 'league')">
                                        <i class="fas fa-trash"></i> Delete
                                    </button>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Add League Modal -->
    <div id="addLeagueModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Add New League</h2>
                <span class="close" onclick="closeModal('addLeagueModal')">&times;</span>
            </div>
            <form id="addLeagueForm" onsubmit="event.preventDefault(); submitForm('addLeagueForm', 'actions/add_league.php')">
                <div class="form-group">
                    <label>League Name</label>
                    <input type="text" name="name" required>
                </div>
                <div class="form-group">
                    <label>Flag URL</label>
                    <input type="url" name="flag_url">
                </div>
                <button type="submit" class="btn-primary">Add League</button>
            </form>
        </div>
    </div>

    <!-- Edit League Modal -->
    <div id="editLeagueModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Edit League</h2>
                <span class="close" onclick="closeModal('editLeagueModal')">&times;</span>
            </div>
            <form id="editLeagueForm" onsubmit="event.preventDefault(); submitForm('editLeagueForm', 'actions/edit_league.php')">
                <input type="hidden" id="edit_league_id" name="id">
                <div class="form-group">
                    <label>League Name</label>
                    <input type="text" id="edit_league_name" name="name" required>
                </div>
                <div class="form-group">
                    <label>Flag URL</label>
                    <input type="url" id="edit_league_flag" name="flag_url">
                </div>
                <button type="submit" class="btn-primary">Update League</button>
            </form>
        </div>
    </div>

    <?php require_once 'footer.php'; ?>

    <script>
        // Add search functionality
        document.querySelector('.search-bar input').addEventListener('input', function(e) {
            const searchText = e.target.value.toLowerCase();
            const rows = document.querySelectorAll('.data-table tbody tr');
            
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(searchText) ? '' : 'none';
            });
        });

        // Edit league functionality
        function editLeague(id, name, flag) {
            document.getElementById('edit_league_id').value = id;
            document.getElementById('edit_league_name').value = name;
            document.getElementById('edit_league_flag').value = flag;
            openModal('editLeagueModal');
        }

        // Delete item functionality
        function deleteItem(id, type) {
            if (confirm('Are you sure you want to delete this ' + type + '?')) {
                // Send request to delete item
            }
        }

        // Open modal functionality
        function openModal(modalId) {
            document.getElementById(modalId).style.display = 'block';
        }

        // Close modal functionality
        function closeModal(modalId) {
            document.getElementById(modalId).style.display = 'none';
        }

        // Submit form functionality
        function submitForm(formId, url) {
            const form = document.getElementById(formId);
            const formData = new FormData(form);
            fetch(url, {
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
