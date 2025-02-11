<?php
class FootballAPI {
    private $api_key = 'bf42421c2e27d2dbaa4adb63c74d5bef';
    private $base_url = 'http://api.football-data.org/v4';
    private $log_file;
    
    // Competiții importante cu ID-uri și nume
    private $competitions = [
        ['id' => 2021, 'name' => 'Premier League'],
        ['id' => 2014, 'name' => 'La Liga'],
        ['id' => 2019, 'name' => 'Serie A'],
        ['id' => 2002, 'name' => 'Bundesliga'],
        ['id' => 2015, 'name' => 'Ligue 1'],
        ['id' => 2016, 'name' => 'Championship'],
        ['id' => 2001, 'name' => 'Champions League']
    ];

    public function __construct() {
        $this->log_file = __DIR__ . '/../logs/football_api_debug.log';
        if (!is_dir(__DIR__ . '/../logs')) {
            mkdir(__DIR__ . '/../logs', 0777, true);
        }
    }

    private function log($message) {
        $timestamp = date('Y-m-d H:i:s');
        $log_message = "[$timestamp] $message\n";
        file_put_contents($this->log_file, $log_message, FILE_APPEND);
    }

    private function makeRequest($endpoint, $params = []) {
        // Verificăm conexiunea la internet
        if (!$this->checkInternetConnection()) {
            error_log("No internet connection available");
            return ['error' => true, 'message' => 'No internet connection. Please check your network.'];
        }

        // Adăugăm diagnosticare pentru parametri și endpoint
        error_log("API Request Endpoint: " . $endpoint);
        error_log("API Request Params: " . json_encode($params));

        $url = $this->base_url . $endpoint;
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'X-Auth-Token: ' . $this->api_key,
            'Content-Type: application/json'
        ]);
        
        // Dezactivăm verificarea SSL temporar pentru debugging
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curl_error = curl_error($ch);
        
        curl_close($ch);
        
        // Jurnalizăm detalii complete despre răspuns
        error_log("HTTP Code: " . $http_code);
        error_log("Curl Error: " . $curl_error);
        error_log("Raw Response: " . $response);
        
        if ($http_code != 200) {
            return [
                'error' => true, 
                'message' => "HTTP Error $http_code", 
                'raw_response' => $response,
                'curl_error' => $curl_error
            ];
        }
        
        $decoded_response = json_decode($response, true);
        
        return $decoded_response;
    }

    // Metodă nouă pentru verificarea conexiunii la internet
    private function checkInternetConnection() {
        $connected = @fsockopen("www.google.com", 80); 
        if ($connected) {
            fclose($connected);
            return true;
        }
        return false;
    }

    public function getTodayMatches() {
        $today = date('Y-m-d');
        $competition_ids = array_column($this->competitions, 'id');
        $competitions = implode(',', $competition_ids);
        
        $endpoint = "/matches?dateFrom={$today}&dateTo={$today}&competitions={$competitions}";
        $this->log("Endpoint: $endpoint");
        
        return $this->makeRequest($endpoint);
    }

    public function formatMatch($match) {
        return [
            'home_team' => $match['homeTeam']['name'] ?? 'Necunoscut',
            'away_team' => $match['awayTeam']['name'] ?? 'Necunoscut',
            'competition' => $match['competition']['name'] ?? 'Competiție Necunoscută',
            'kickoff_time' => date('H:i', strtotime($match['utcDate'])),
            'status' => $match['status'] ?? 'PROGRAMAT',
            'score' => [
                'home' => $match['score']['fullTime']['home'] ?? '-',
                'away' => $match['score']['fullTime']['away'] ?? '-'
            ]
        ];
    }
}

function getCachedMatches($api) {
    $cache_file = __DIR__ . '/../cache/matches.json';
    $cache_time = 300; // 5 minute
    
    if (!is_dir(__DIR__ . '/../cache')) {
        mkdir(__DIR__ . '/../cache', 0777, true);
    }

    // Verificăm cache-ul
    if (file_exists($cache_file) && (time() - filemtime($cache_file)) < $cache_time) {
        $cached_data = json_decode(file_get_contents($cache_file), true);
        if ($cached_data && !isset($cached_data['error'])) {
            return $cached_data;
        }
    }

    // Luăm date noi
    $matches = $api->getTodayMatches();
    
    if (!isset($matches['error'])) {
        file_put_contents($cache_file, json_encode($matches));
    }
    
    return $matches;
}
?>
