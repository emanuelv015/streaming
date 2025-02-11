<?php
session_start();
require_once '../inc/config.php';
require_once '../inc/db_config.php';
require_once 'admin_auth.php';

// Verifică autentificarea
requireAdminLogin();

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $country = trim($_POST['country']);
    $logo_url = trim($_POST['logo_url']);
    $status = $_POST['status'];

    if (empty($name)) {
        $error = "League name is required";
    } else {
        // Verifică dacă liga există deja
        $check_query = "SELECT id FROM leagues WHERE name = ?";
        $stmt = $conn->prepare($check_query);
        $stmt->bind_param("s", $name);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $error = "A league with this name already exists";
        } else {
            // Inserează liga nouă
            $insert_query = "INSERT INTO leagues (name, country, logo_url, status) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($insert_query);
            $stmt->bind_param("ssss", $name, $country, $logo_url, $status);

            if ($stmt->execute()) {
                $message = "League added successfully!";
                // Redirecționează după 2 secunde
                header("refresh:2;url=leagues.php");
            } else {
                $error = "Error adding league: " . $conn->error;
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add League - <?php echo $site_title; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css/admin.css">
    <style>
        .form-container {
            max-width: 600px;
            margin: 0 auto;
            padding: 2rem;
            background: var(--card-bg);
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: var(--text-color);
            font-weight: 500;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid var(--border-color);
            border-radius: 4px;
            background: var(--input-bg);
            color: var(--text-color);
        }

        .form-group input:focus,
        .form-group select:focus {
            border-color: var(--primary-color);
            outline: none;
        }

        .alert {
            padding: 1rem;
            border-radius: 4px;
            margin-bottom: 1rem;
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
    </style>
</head>
<body>
    <div class="admin-dashboard">
        <?php include 'sidebar.php'; ?>
        
        <div class="main-content">
            <header>
                <h1>Add New League</h1>
                <div class="header-actions">
                    <a href="leagues.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i>
                        Back to Leagues
                    </a>
                </div>
            </header>

            <div class="content-wrapper">
                <?php if ($message): ?>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i>
                        <?php echo $message; ?>
                    </div>
                <?php endif; ?>

                <?php if ($error): ?>
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle"></i>
                        <?php echo $error; ?>
                    </div>
                <?php endif; ?>

                <div class="form-container">
                    <form method="POST" action="">
                        <div class="form-group">
                            <label for="name">League Name *</label>
                            <input type="text" id="name" name="name" required 
                                   value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>">
                        </div>

                        <div class="form-group">
                            <label for="country">Country</label>
                            <input type="text" id="country" name="country"
                                   value="<?php echo isset($_POST['country']) ? htmlspecialchars($_POST['country']) : ''; ?>">
                        </div>

                        <div class="form-group">
                            <label for="logo_url">Logo URL</label>
                            <input type="url" id="logo_url" name="logo_url"
                                   value="<?php echo isset($_POST['logo_url']) ? htmlspecialchars($_POST['logo_url']) : ''; ?>"
                                   placeholder="https://example.com/logo.png">
                        </div>

                        <div class="form-group">
                            <label for="status">Status</label>
                            <select id="status" name="status">
                                <option value="active" <?php echo (isset($_POST['status']) && $_POST['status'] === 'active') ? 'selected' : ''; ?>>Active</option>
                                <option value="inactive" <?php echo (isset($_POST['status']) && $_POST['status'] === 'inactive') ? 'selected' : ''; ?>>Inactive</option>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-plus"></i>
                            Add League
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
