<?php
include 'inc/db_config.php';
include 'inc/head.php';

// Obține toate evenimentele
$sql = "SELECT * FROM evenimente ORDER BY data, ora";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Evenimente Live</title>
    <link rel="stylesheet" href="css/lshd.css">
    <style>
        .events-container {
            max-width: 1200px;
            margin: 50px auto;
            padding: 20px;
        }
        .event-card {
            background: #fff;
            margin-bottom: 20px;
            padding: 15px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .event-info {
            flex: 1;
        }
        .event-teams {
            font-size: 1.2em;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .event-datetime {
            color: #666;
        }
        .event-sport {
            background: #ff5529;
            color: white;
            padding: 5px 10px;
            border-radius: 3px;
            margin-right: 15px;
        }
        .watch-btn {
            background: #ff5529;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 4px;
            transition: background 0.3s;
        }
        .watch-btn:hover {
            background: #e64a23;
        }
    </style>
</head>
<body>
    <?php include 'layouts/header.php'; ?>

    <div class="events-container">
        <h1>Evenimente Live</h1>

        <?php
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                ?>
                <div class="event-card">
                    <span class="event-sport"><?php echo ucfirst($row['sport']); ?></span>
                    <div class="event-info">
                        <div class="event-teams">
                            <?php echo htmlspecialchars($row['echipa1']); ?> vs <?php echo htmlspecialchars($row['echipa2']); ?>
                        </div>
                        <div class="event-datetime">
                            <?php 
                            $data = date('d-m-Y', strtotime($row['data']));
                            $ora = date('H:i', strtotime($row['ora']));
                            echo $data . ' ' . $ora; 
                            ?>
                        </div>
                    </div>
                    <a href="<?php echo htmlspecialchars($row['link_stream']); ?>" class="watch-btn" target="_blank">
                        Urmărește Live
                    </a>
                </div>
                <?php
            }
        } else {
            echo "<p>Nu există evenimente programate momentan.</p>";
        }
        ?>
    </div>

    <?php include 'layouts/footer.php'; ?>
</body>
</html>
