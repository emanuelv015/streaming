<?php
header("Content-Type: application/xml; charset=utf-8");
require_once 'inc/config.php';
require_once 'inc/db_config.php';

echo '<?xml version="1.0" encoding="UTF-8"?>';
?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <?php
    // Pentru fiecare meci
    $query = "SELECT slug FROM matches WHERE date >= CURDATE()";
    $result = $conn->query($query);
    while($row = $result->fetch_assoc()) {
        echo "<url>\n";
        echo "<loc>https://getsportnews.uk/stream/" . $row['slug'] . "</loc>\n";
        echo "<lastmod>" . date('Y-m-d') . "</lastmod>\n";
        echo "<changefreq>hourly</changefreq>\n";
        echo "<priority>0.8</priority>\n";
        echo "</url>\n";
    }
    ?>
</urlset>
<?php
$conn->close();
?> 