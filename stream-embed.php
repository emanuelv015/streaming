<?php
$url = $_GET['url'] ?? '';
if (empty($url)) {
    die('No stream URL provided');
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body, html {
            margin: 0;
            padding: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
        }
        iframe {
            width: 100%;
            height: 100%;
            border: 0;
        }
    </style>
</head>
<body>
    <iframe 
        src="<?php echo htmlspecialchars(urldecode($url)); ?>"
        frameborder="0"
        allowfullscreen="true"
        scrolling="no"
        allow="encrypted-media; autoplay; fullscreen"
        style="width:100%; height:100%;">
    </iframe>
</body>
</html> 