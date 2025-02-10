<?php
$url = $_GET['url'] ?? '';
if (empty($url)) {
    die('No URL provided');
}

// Ia conținutul paginii
$html = file_get_contents($url);

// Găsește iframe-ul
if (preg_match('/<iframe[^>]*>(.*?)<\/iframe>/is', $html, $matches)) {
    echo $matches[0]; // Returnează iframe-ul găsit
} else {
    echo "No iframe found";
} 