<?php
include 'inc/db_config.php';

// Preluăm sportul din query string
$sport = isset($_GET['sport']) ? $_GET['sport'] : 'all';

// Construim query-ul în funcție de sport
$sql = "SELECT * FROM matches WHERE DATE(match_date) = CURRENT_DATE()";
if ($sport !== 'all') {
    $sql .= " AND sport = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $sport);
} else {
    $stmt = $conn->prepare($sql);
}

if (!$stmt->execute()) {
    echo "<div class='error'>Error loading matches.</div>";
    exit;
}

$result = $stmt->get_result();
?>

<div class="today-matches">
    Today Matches (<?php echo date('d-m-Y'); ?>)
</div>

<?php if ($result && $result->num_rows > 0): ?>
    <?php while($match = $result->fetch_assoc()): ?>
        <div class="match-row">
            <img src="https://flagcdn.com/24x18/<?php echo htmlspecialchars($match['league_flag']); ?>.png" 
                 class="league-flag" 
                 alt="<?php echo htmlspecialchars($match['league']); ?>">
            <div class="league-name"><?php echo htmlspecialchars($match['league']); ?></div>
            <div class="teams">
                <span><?php echo htmlspecialchars($match['team1']); ?></span>
                <span class="vs">vs</span>
                <span><?php echo htmlspecialchars($match['team2']); ?></span>
            </div>
            <span class="match-status">
                LIVE
            </span>
            <a href="stream.php?match_id=<?php echo $match['id']; ?>" class="watch-btn">
                <i class="fas fa-play"></i> Watch
            </a>
        </div>
    <?php endwhile; ?>
<?php else: ?>
    <div class="no-matches">
        No matches found for selected sport.
    </div>
<?php endif; ?>
