<?php
require_once 'inc/config.php';
require_once 'inc/db_config.php';

header('Content-Type: text/xml');

// Create XML document
$xml = new DOMDocument('1.0', 'UTF-8');
$xml->formatOutput = true;

// Create urlset element
$urlset = $xml->createElement('urlset');
$urlset->setAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');
$urlset->setAttribute('xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
$urlset->setAttribute('xsi:schemaLocation', 'http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd');

// Add static pages
$staticPages = array(
    '' => '1.0',
    'live-scores' => '0.8',
    'contact' => '0.6',
    'privacy-policy' => '0.6',
    'terms' => '0.6'
);

foreach ($staticPages as $page => $priority) {
    $url = $xml->createElement('url');
    
    $loc = $xml->createElement('loc', rtrim($base_url, '/') . '/' . $page);
    $url->appendChild($loc);
    
    $lastmod = $xml->createElement('lastmod', date('Y-m-d'));
    $url->appendChild($lastmod);
    
    $changefreq = $xml->createElement('changefreq', 'daily');
    $url->appendChild($changefreq);
    
    $priorityElement = $xml->createElement('priority', $priority);
    $url->appendChild($priorityElement);
    
    $urlset->appendChild($url);
}

// Add matches from database
$stmt = $conn->prepare("
    SELECT slug, date 
    FROM matches 
    WHERE date >= DATE_SUB(NOW(), INTERVAL 1 DAY)
    ORDER BY date DESC
");
$stmt->execute();
$result = $stmt->get_result();

while ($match = $result->fetch_assoc()) {
    $url = $xml->createElement('url');
    
    $loc = $xml->createElement('loc', rtrim($base_url, '/') . '/stream/' . $match['slug']);
    $url->appendChild($loc);
    
    $lastmod = $xml->createElement('lastmod', date('Y-m-d', strtotime($match['date'])));
    $url->appendChild($lastmod);
    
    $changefreq = $xml->createElement('changefreq', 'always');
    $url->appendChild($changefreq);
    
    $priority = $xml->createElement('priority', '0.9');
    $url->appendChild($priority);
    
    $urlset->appendChild($url);
}

// Add leagues from database
$stmt = $conn->prepare("SELECT slug FROM leagues");
$stmt->execute();
$result = $stmt->get_result();

while ($league = $result->fetch_assoc()) {
    $url = $xml->createElement('url');
    
    $loc = $xml->createElement('loc', rtrim($base_url, '/') . '/league/' . $league['slug']);
    $url->appendChild($loc);
    
    $lastmod = $xml->createElement('lastmod', date('Y-m-d'));
    $url->appendChild($lastmod);
    
    $changefreq = $xml->createElement('changefreq', 'daily');
    $url->appendChild($changefreq);
    
    $priority = $xml->createElement('priority', '0.7');
    $url->appendChild($priority);
    
    $urlset->appendChild($url);
}

$xml->appendChild($urlset);

// Save the sitemap
$xml->save('sitemap.xml');

// Create a cron job to run this daily
echo "Sitemap generated successfully!\n";
?>
