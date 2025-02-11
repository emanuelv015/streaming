<?php
require_once '../inc/config.php';
require_once '../inc/db_config.php';

// Debug flag paths
$upload_dir = '../assets/img/flags/';
$absolute_upload_dir = realpath($upload_dir);
error_log("Upload directory: " . $absolute_upload_dir);
error_log("Base URL: " . $base_url);

// Create upload directory if it doesn't exist
if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0777, true);
    error_log("Created upload directory: " . $upload_dir);
}

// Handle form submission for adding/editing league
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (isset($_POST['action'])) {
            if ($_POST['action'] === 'add') {
                $name = $_POST['league_name'];
                $flag_url = '';

                // Handle file upload
                if (isset($_FILES['flag']) && $_FILES['flag']['error'] === 0) {
                    $allowed = [
                        'jpg' => 'image/jpeg',
                        'jpeg' => 'image/jpeg',
                        'png' => 'image/png',
                        'gif' => 'image/gif',
                        'webp' => 'image/webp',
                        'svg' => 'image/svg+xml'
                    ];
                    $filename = $_FILES['flag']['name'];
                    $filetype = $_FILES['flag']['type'];
                    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

                    error_log("Uploading file: " . $filename . " of type: " . $filetype);

                    if (isset($allowed[$ext])) {
                        $newname = strtolower(str_replace(' ', '-', $name)) . '.' . $ext;
                        $uploadfile = $upload_dir . $newname;
                        
                        if (move_uploaded_file($_FILES['flag']['tmp_name'], $uploadfile)) {
                            $flag_url = 'assets/img/flags/' . $newname;
                            error_log("File uploaded successfully to: " . $uploadfile);
                            error_log("Flag URL set to: " . $flag_url);
                        } else {
                            throw new Exception("Error uploading file to: " . $uploadfile);
                        }
                    } else {
                        throw new Exception("Invalid file type. Allowed types: JPG, JPEG, PNG, GIF, WEBP, SVG");
                    }
                } 
                // Handle flag URL
                elseif (!empty($_POST['flag_url'])) {
                    $flag_url = $_POST['flag_url'];
                    error_log("Using provided flag URL: " . $flag_url);
                }

                $stmt = $conn->prepare("INSERT INTO leagues (name, flag_url) VALUES (?, ?)");
                if (!$stmt) {
                    throw new Exception($conn->error);
                }
                $stmt->bind_param('ss', $name, $flag_url);
                
                if ($stmt->execute()) {
                    $success_message = "League added successfully!";
                    error_log("League added with flag_url: " . $flag_url);
                } else {
                    throw new Exception($stmt->error);
                }
            }
            elseif ($_POST['action'] === 'edit') {
                $id = $_POST['league_id'];
                $name = $_POST['league_name'];
                $flag_url = isset($_POST['current_flag']) ? $_POST['current_flag'] : '';

                error_log("Editing league ID: " . $id . " with current flag: " . $flag_url);

                // Handle file upload
                if (isset($_FILES['flag']) && $_FILES['flag']['error'] === 0) {
                    $allowed = [
                        'jpg' => 'image/jpeg',
                        'jpeg' => 'image/jpeg',
                        'png' => 'image/png',
                        'gif' => 'image/gif',
                        'webp' => 'image/webp',
                        'svg' => 'image/svg+xml'
                    ];
                    $filename = $_FILES['flag']['name'];
                    $filetype = $_FILES['flag']['type'];
                    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

                    error_log("Uploading new flag: " . $filename . " of type: " . $filetype);

                    if (isset($allowed[$ext])) {
                        $newname = strtolower(str_replace(' ', '-', $name)) . '.' . $ext;
                        $uploadfile = $upload_dir . $newname;
                        
                        if (move_uploaded_file($_FILES['flag']['tmp_name'], $uploadfile)) {
                            // Delete old flag if exists and is local
                            if (!empty($_POST['current_flag'])) {
                                $old_flag = '../' . $_POST['current_flag'];
                                if (file_exists($old_flag)) {
                                    unlink($old_flag);
                                    error_log("Deleted old flag: " . $old_flag);
                                }
                            }
                            $flag_url = 'assets/img/flags/' . $newname;
                            error_log("New flag uploaded to: " . $uploadfile);
                            error_log("Flag URL updated to: " . $flag_url);
                        } else {
                            throw new Exception("Error uploading file to: " . $uploadfile);
                        }
                    } else {
                        throw new Exception("Invalid file type. Allowed types: JPG, JPEG, PNG, GIF, WEBP, SVG");
                    }
                }
                // Handle flag URL
                elseif (!empty($_POST['flag_url'])) {
                    $flag_url = $_POST['flag_url'];
                    error_log("Using provided flag URL: " . $flag_url);
                }

                $stmt = $conn->prepare("UPDATE leagues SET name = ?, flag_url = ? WHERE id = ?");
                if (!$stmt) {
                    throw new Exception($conn->error);
                }
                $stmt->bind_param('ssi', $name, $flag_url, $id);
                
                if ($stmt->execute()) {
                    $success_message = "League updated successfully!";
                    error_log("League updated with flag_url: " . $flag_url);
                } else {
                    throw new Exception($stmt->error);
                }
            }
        }
    } catch (Exception $e) {
        $error_message = $e->getMessage();
        error_log("Error in flags.php: " . $e->getMessage());
    }
}

// Get all leagues
$leagues = [];
$result = $conn->query("SELECT * FROM leagues ORDER BY name");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $leagues[] = $row;
    }
}

// Get league for editing if requested
$edit_league = null;
if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
    $edit_id = (int)$_GET['edit'];
    foreach ($leagues as $league) {
        if ($league['id'] === $edit_id) {
            $edit_league = $league;
            break;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Leagues & Flags - Admin Panel</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --dark-bg: #0f1923;
            --card-bg: #1a242d;
            --primary: #3498db;
            --secondary: #2ecc71;
            --danger: #e74c3c;
            --text: #ecf0f1;
            --text-muted: #95a5a6;
            --border: #2c3e50;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Arial, sans-serif;
        }

        body {
            background: var(--dark-bg);
            color: var(--text);
            line-height: 1.6;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 40px 20px;
        }

        .page-header {
            text-align: center;
            margin-bottom: 40px;
        }

        .page-header h1 {
            font-size: 2.5em;
            margin-bottom: 10px;
            color: var(--primary);
        }

        .section {
            background: var(--card-bg);
            border-radius: 10px;
            padding: 30px;
            margin-bottom: 30px;
            border: 1px solid var(--border);
        }

        .section-title {
            font-size: 1.5em;
            margin-bottom: 20px;
            color: var(--text);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: var(--text-muted);
        }

        .form-control {
            width: 100%;
            padding: 12px;
            background: var(--dark-bg);
            border: 1px solid var(--border);
            border-radius: 5px;
            color: var(--text);
            font-size: 14px;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary);
        }

        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background: var(--primary);
            color: white;
        }

        .btn-primary:hover {
            background: #2980b9;
        }

        .btn-danger {
            background: var(--danger);
            color: white;
        }

        .btn-danger:hover {
            background: #c0392b;
        }

        .leagues-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
            margin-top: 30px;
        }

        .league-card {
            background: rgba(255,255,255,0.05);
            border-radius: 8px;
            padding: 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 15px;
            text-align: center;
        }

        .league-flag {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            object-fit: cover;
        }

        .league-name {
            font-size: 1.2em;
            font-weight: 500;
        }

        .league-actions {
            display: flex;
            gap: 10px;
        }

        .alert {
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            text-align: center;
        }

        .alert-success {
            background: rgba(46, 204, 113, 0.1);
            border: 1px solid var(--secondary);
            color: var(--secondary);
        }

        .alert-error {
            background: rgba(231, 76, 60, 0.1);
            border: 1px solid var(--danger);
            color: var(--danger);
        }

        .or-separator {
            text-align: center;
            margin: 20px 0;
            color: var(--text-muted);
        }

        @media (max-width: 768px) {
            .leagues-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="page-header">
            <h1>Manage Leagues & Flags</h1>
            <p>Add or edit leagues and their associated flags</p>
        </div>

        <?php if (isset($success_message)): ?>
            <div class="alert alert-success"><?php echo $success_message; ?></div>
        <?php endif; ?>

        <?php if (isset($error_message)): ?>
            <div class="alert alert-error"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <div class="section">
            <h2 class="section-title">
                <i class="fas fa-plus-circle"></i>
                <?php echo $edit_league ? 'Edit League' : 'Add New League'; ?>
            </h2>

            <form action="" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" value="<?php echo $edit_league ? 'edit' : 'add'; ?>">
                <?php if ($edit_league): ?>
                    <input type="hidden" name="league_id" value="<?php echo $edit_league['id']; ?>">
                    <input type="hidden" name="current_flag" value="<?php echo isset($edit_league['flag_url']) ? $edit_league['flag_url'] : ''; ?>">
                <?php endif; ?>

                <div class="form-group">
                    <label for="league_name">League Name:</label>
                    <input type="text" class="form-control" id="league_name" name="league_name" 
                           value="<?php echo $edit_league ? htmlspecialchars($edit_league['name']) : ''; ?>" required>
                </div>

                <div class="form-group">
                    <label for="flag">Upload Flag (JPG, JPEG, PNG, GIF, WEBP, SVG):</label>
                    <input type="file" class="form-control" id="flag" name="flag" accept="image/*">
                </div>

                <div class="or-separator">OR</div>

                <div class="form-group">
                    <label for="flag_url">Flag URL:</label>
                    <input type="url" class="form-control" id="flag_url" name="flag_url" 
                           placeholder="https://example.com/flag.jpg">
                </div>

                <button type="submit" class="btn btn-primary">
                    <?php echo $edit_league ? 'Update League' : 'Add League'; ?>
                </button>

                <?php if ($edit_league): ?>
                    <a href="flags.php" class="btn btn-danger" style="margin-left: 10px;">Cancel Edit</a>
                <?php endif; ?>
            </form>
        </div>

        <div class="section">
            <h2 class="section-title">
                <i class="fas fa-list"></i>
                Existing Leagues
            </h2>

            <div class="leagues-grid">
                <?php foreach ($leagues as $league): ?>
                    <div class="league-card">
                        <?php if (!empty($league['flag_url'])): ?>
                            <img src="<?php echo htmlspecialchars($base_url . $league['flag_url']); ?>" 
                                 alt="<?php echo htmlspecialchars($league['name']); ?> Flag" 
                                 class="league-flag">
                        <?php else: ?>
                            <div class="league-flag" style="background: var(--card-bg);">
                                <i class="fas fa-flag" style="font-size: 24px;"></i>
                            </div>
                        <?php endif; ?>
                        
                        <div class="league-name"><?php echo htmlspecialchars($league['name']); ?></div>
                        
                        <div class="league-actions">
                            <a href="?edit=<?php echo $league['id']; ?>" class="btn btn-primary">Edit</a>
                            <button onclick="deleteLeague(<?php echo $league['id']; ?>)" class="btn btn-danger">Delete</button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <script>
    function deleteLeague(id) {
        if (confirm('Are you sure you want to delete this league?')) {
            fetch('delete_league.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ id: id })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Error deleting league');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error deleting league');
            });
        }
    }
    </script>
</body>
</html>
