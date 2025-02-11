<?php  
    $site = "getsportnews.uk";
    
    // Definim sporturile disponibile
    $sports = array(
        array(
            'name' => 'All Sports',
            'tag' => '/',
            'icon' => '<i class="fas fa-globe"></i>'
        ),
        array(
            'name' => 'Football',
            'tag' => '/football',
            'icon' => '<i class="fas fa-futbol"></i>'
        ),
        array(
            'name' => 'Baseball',
            'tag' => '/baseball',
            'icon' => '<i class="fas fa-baseball-ball"></i>'
        ),
        array(
            'name' => 'Ice Hockey',
            'tag' => '/hockey',
            'icon' => '<i class="fas fa-hockey-puck"></i>'
        ),
        array(
            'name' => 'Basketball',
            'tag' => '/basketball',
            'icon' => '<i class="fas fa-basketball-ball"></i>'
        ),
        array(
            'name' => 'Rugby',
            'tag' => '/rugby',
            'icon' => '<i class="fas fa-football-ball"></i>'
        ),
        array(
            'name' => 'Boxing',
            'tag' => '/boxing',
            'icon' => '<i class="fas fa-fist-raised"></i>'
        )
    );

    // Setăm pagina curentă bazată pe URL
    $current_url = $_SERVER['REQUEST_URI'];
    $url_parts = explode('/', $current_url);
    $page = end($url_parts);
    if(empty($page)) {
        $page = 'All Sports';
    }
?>