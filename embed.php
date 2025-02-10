<?php
header("Referrer-Policy: origin");
$url = $_GET['url'] ?? '';
?>
<!DOCTYPE html>
<html>
<head>
    <meta name="referrer" content="origin">
    <style>
        body, html { margin: 0; padding: 0; height: 100%; }
        iframe { width: 100%; height: 100%; border: 0; }
    </style>
</head>
<body>
    <iframe 
        src="<?php echo htmlspecialchars($url); ?>"
        frameborder="0"
        scrolling="no"
        allow="encrypted-media"
        allowfullscreen>
    </iframe>
</body>
</html> 