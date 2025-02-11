<?php
// Site configuration
$is_production = false;

// Check if we're on the production server
if ($_SERVER['HTTP_HOST'] === 'www.getsportnews.uk' || 
    $_SERVER['HTTP_HOST'] === 'getsportnews.uk') {
    $is_production = true;
}

// Set base URL based on environment
if ($is_production) {
    define('BASE_URL', 'https://www.getsportnews.uk');
} else {
    define('BASE_URL', '/streaming/streamthunder-demo-website');
}

// Site settings
define('SITE_NAME', 'GetSportNews');
define('SITE_DOMAIN', 'getsportnews.uk');

// Function to get full URL for a path
function get_url($path = '') {
    $path = ltrim($path, '/');
    return BASE_URL . ($path ? "/$path" : '');
}
?>
