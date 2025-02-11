<?php
$translations = [
    'en' => [
        'watch_live' => 'Watch Live',
        'watch_now' => 'Watch Now',
        'loading' => 'If the player doesn\'t load immediately, please wait a few seconds...',
        'todays_matches' => 'Today\'s Matches',
        'stream_preparing' => 'The stream is being prepared for the best viewing experience'
    ],
    'ro' => [
        'watch_live' => 'Urmărește Live',
        'watch_now' => 'Urmărește Acum',
        'loading' => 'Dacă player-ul nu se încarcă imediat, vă rugăm așteptați câteva secunde...',
        'todays_matches' => 'Meciurile de Astăzi',
        'stream_preparing' => 'Stream-ul se pregătește pentru cea mai bună experiență de vizionare'
    ],
    'fr' => [
        'watch_live' => 'Regarder en Direct',
        'watch_now' => 'Regarder Maintenant',
        'loading' => 'Si le lecteur ne se charge pas immédiatement, veuillez patienter quelques secondes...',
        'todays_matches' => 'Matchs du Jour',
        'stream_preparing' => 'Le flux est en cours de préparation pour la meilleure expérience de visionnage'
    ],
    'es' => [
        'watch_live' => 'Ver en Vivo',
        'watch_now' => 'Ver Ahora',
        'loading' => 'Si el reproductor no carga inmediatamente, espere unos segundos...',
        'todays_matches' => 'Partidos de Hoy',
        'stream_preparing' => 'La transmisión se está preparando para la mejor experiencia de visualización'
    ]
];

function t($key) {
    global $translations, $lang;
    return $translations[$lang][$key] ?? $translations['en'][$key] ?? $key;
} 