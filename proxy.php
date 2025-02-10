<?php
session_start();
header('Access-Control-Allow-Origin: *');

if (isset($_GET['url'])) {
    $url = base64_decode($_GET['url']);
    
    // Setăm headers pentru a părea un browser normal
    $headers = [
        'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
        'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
        'Accept-Language: en-US,en;q=0.5',
        'Connection: keep-alive',
        'Upgrade-Insecure-Requests: 1',
        'Cache-Control: max-age=0',
        'Referer: https://www.google.com/'
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $response = curl_exec($ch);
    $contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
    curl_close($ch);

    header("Content-Type: $contentType");
    echo $response;
    exit;
}

header("Referrer-Policy: strict-origin");
$url = $_GET['url'] ?? '';
?>
<!DOCTYPE html>
<html>
<head>
    <meta name="referrer" content="strict-origin">
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
        src="<?php echo htmlspecialchars($url); ?>"
        frameborder="0"
        scrolling="no"
        allowfullscreen>
    </iframe>
</body>
</html> 