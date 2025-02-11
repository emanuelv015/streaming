<?php
function pingSearchEngines($url) {
    $engines = array(
        "http://www.google.com/webmasters/tools/ping?sitemap=",
        "http://www.bing.com/ping?sitemap="
    );
    
    foreach($engines as $engine) {
        $ping = $engine . urlencode($url);
        file_get_contents($ping);
    }
}

// Folosește-l când adaugi meciuri noi
pingSearchEngines("https://getsportnews.uk/sitemap.xml"); 