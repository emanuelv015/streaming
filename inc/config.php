<?php
// Site configuration
$config = array(
    'site_url' => 'https://www.getsportnews.uk',  // URL-ul site-ului în producție
    'site_name' => 'GETSPORTNEWS.UK',
    'site_description' => 'Watch live football streaming',
    'admin_email' => 'admin@getsportnews.uk',
    'favicon_path' => '/images/favicon.ico'  // Adăugăm calea către favicon
);

// Global variables
global $site_title;
$site_title = $config['site_name'];

// Function to get URL
function get_url($path = '') {
    global $config;
    $path = ltrim($path, '/');
    return rtrim($config['site_url'], '/') . '/' . $path;
}

// Function to get favicon URL
function get_favicon_url() {
    global $config;
    return get_url($config['favicon_path']);
}

// Set timezone
date_default_timezone_set('Europe/Bucharest');

// Production settings
error_reporting(0);
ini_set('display_errors', 0);

function getUserLanguage() {
    $accepted_languages = ['en', 'ro', 'fr', 'es'];
    
    // Check URL parameter first
    if (isset($_GET['lang']) && in_array($_GET['lang'], $accepted_languages)) {
        return $_GET['lang'];
    }
    
    // Check browser language
    $browser_lang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
    if (in_array($browser_lang, $accepted_languages)) {
        return $browser_lang;
    }
    
    // Default to English
    return 'en';
}
?>
