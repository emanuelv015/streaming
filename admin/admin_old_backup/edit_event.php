<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once 'admin_auth.php';
check_admin_access(); 

include '../inc/conf.php';
include '../inc/db_config.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
    header('Content-Type: application/json');
    $id = $_GET['id'];
    
    $sql = "SELECT m.*, 
                   ht.name as home_team_name, ht.logo_url as home_team_logo,
                   at.name as away_team_name, at.logo_url as away_team_logo,
                   GROUP_CONCAT(
                       CONCAT_WS('|', ss.id, ss.source_url, ss.language, ss.source_type, ss.source_name)
                       SEPARATOR ';;'
                   ) as stream_sources
            FROM matches m
            LEFT JOIN teams ht ON m.home_team_id = ht.id
            LEFT JOIN teams at ON m.away_team_id = at.id
            LEFT JOIN stream_sources ss ON m.id = ss.match_id
            WHERE m.id = ?
            GROUP BY m.id";
            
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        echo json_encode(['success' => false, 'error' => $conn->error]);
        exit;
    }
    
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $event = $result->fetch_assoc();
    
    if ($event) {
        $event['match_date'] = date('Y-m-d', strtotime($event['match_time']));
        $event['match_time'] = date('H:i', strtotime($event['match_time']));
        echo json_encode(['success' => true, 'event' => $event]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Event not found']);
    }
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $contentType = isset($_SERVER["CONTENT_TYPE"]) ? trim($_SERVER["CONTENT_TYPE"]) : '';
    
    if (strpos($contentType, 'application/json') !== false) {
        $data = json_decode(file_get_contents('php://input'), true);
    } else {
        $data = $_POST;
    }
    
    if (empty($data)) {
        echo json_encode(['success' => false, 'error' => 'No data received']);
        exit;
    }

    // Validăm ID-urile echipelor
    $team_check = $conn->prepare("SELECT COUNT(*) as count FROM teams WHERE id IN (?, ?)");
    $team_check->bind_param("ii", $data['home_team_id'], $data['away_team_id']);
    $team_check->execute();
    $team_result = $team_check->get_result()->fetch_assoc();
    
    if ($team_result['count'] != 2) {
        echo json_encode(['success' => false, 'error' => 'Invalid team IDs']);
        exit;
    }

    // Începe tranzacția
    $conn->begin_transaction();
    try {
        // Combine date and time
        $match_time = date('Y-m-d H:i:s', strtotime($data['match_date'] . ' ' . $data['match_time']));
        
        if (isset($data['id']) && !empty($data['id'])) {
            $sql = "UPDATE matches SET 
                    league = ?, 
                    home_team_id = ?,
                    away_team_id = ?,
                    match_time = ?,
                    sport = ?,
                    status = ?
                    WHERE id = ?";
                    
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("siisssi", 
                $data['league'],
                $data['home_team_id'],
                $data['away_team_id'],
                $match_time,
                $data['sport'],
                $data['status'],
                $data['id']
            );
            $stmt->execute();

            // Șterge sursele vechi
            $delete_sources = $conn->prepare("DELETE FROM stream_sources WHERE match_id = ?");
            $delete_sources->bind_param("i", $data['id']);
            $delete_sources->execute();
        } else {
            $sql = "INSERT INTO matches (
                    league, 
                    home_team_id,
                    away_team_id,
                    match_time,
                    sport,
                    status
                ) VALUES (?, ?, ?, ?, ?, ?)";
                    
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("siisss", 
                $data['league'],
                $data['home_team_id'],
                $data['away_team_id'],
                $match_time,
                $data['sport'],
                $data['status']
            );
            $stmt->execute();
            $data['id'] = $conn->insert_id;
        }

        // Adaugă noile surse
        if (!empty($data['sources'])) {
            $source_sql = "INSERT INTO stream_sources (match_id, source_url, language, source_type, source_name) VALUES (?, ?, ?, ?, ?)";
            $source_stmt = $conn->prepare($source_sql);
            
            foreach ($data['sources'] as $source) {
                $source_stmt->bind_param("issss", 
                    $data['id'],
                    $source['url'],
                    $source['language'],
                    $source['type'],
                    $source['name']
                );
                $source_stmt->execute();
            }
        }

        $conn->commit();
        
        if ($contentType === 'application/json') {
            header('Content-Type: application/json');
            echo json_encode(['success' => true]);
        } else {
            header('Location: index.php?success=1');
        }
    } catch (Exception $e) {
        $conn->rollback();
        if ($contentType === 'application/json') {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        } else {
            header('Location: index.php?error=' . urlencode($e->getMessage()));
        }
    }
    exit;
}
?>
