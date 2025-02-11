<?php
require_once 'Mobile_Detect.php';
require_once 'db_config.php';

// Adaugă temporar pentru debugging
error_log("Tracking initialized on: " . $_SERVER['REQUEST_URI']);

function trackPageView() {
    global $conn;
    
    // Verifică dacă suntem în panoul de admin
    if (strpos($_SERVER['REQUEST_URI'], '/admin/') !== false) {
        return; // Nu urmări vizitele din admin
    }
    
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    if (!isset($_SESSION['visit_id'])) {
        $_SESSION['visit_id'] = session_id();
        $_SESSION['entry_time'] = time();
    }

    $session_id = $_SESSION['visit_id'];
    $ip_address = $_SERVER['REMOTE_ADDR'];
    $user_agent = $_SERVER['HTTP_USER_AGENT'];
    $page_url = $_SERVER['REQUEST_URI'];
    $referrer = $_SERVER['HTTP_REFERER'] ?? '';
    
    // Detectare dispozitiv și browser
    $detect = new Mobile_Detect;
    $device_type = ($detect->isMobile() ? ($detect->isTablet() ? 'tablet' : 'phone') : 'desktop');
    
    // Detectare browser și OS
    $browser = get_browser_name($_SERVER['HTTP_USER_AGENT']);
    $os = get_os($_SERVER['HTTP_USER_AGENT']);
    
    // Calculare timp petrecut
    $time_spent = isset($_SESSION['entry_time']) ? (time() - $_SESSION['entry_time']) : 0;
    
    // Actualizare sau inserare vizită
    $stmt = $conn->prepare("
        INSERT INTO user_visits 
        (session_id, ip_address, user_agent, page_url, referrer_url, 
         device_type, browser, os, entry_time, time_spent) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW(), ?)
    ");
    $stmt->bind_param("ssssssssi", 
        $session_id, $ip_address, $user_agent, $page_url, 
        $referrer, $device_type, $browser, $os, $time_spent
    );
    $stmt->execute();
}

function trackStreamView($match_id) {
    global $conn;
    
    // Verifică dacă avem conexiune la bază de date
    if (!$conn) {
        error_log("No database connection in trackStreamView");
        return false;
    }

    try {
        // Verifică dacă există deja o înregistrare pentru ziua curentă
        $check_query = "SELECT id, views FROM stream_stats 
                       WHERE match_id = ? AND created_at = CURDATE()";
        
        $check_stmt = $conn->prepare($check_query);
        if ($check_stmt === false) {
            error_log("Prepare failed in trackStreamView: " . $conn->error);
            return false;
        }

        $check_stmt->bind_param('i', $match_id);
        $check_stmt->execute();
        $result = $check_stmt->get_result();
        
        if ($result->num_rows > 0) {
            // Update existing record
            $row = $result->fetch_assoc();
            $update_query = "UPDATE stream_stats 
                           SET views = views + 1 
                           WHERE id = ?";
            
            $update_stmt = $conn->prepare($update_query);
            if ($update_stmt === false) {
                error_log("Prepare failed for update in trackStreamView: " . $conn->error);
                return false;
            }

            $update_stmt->bind_param('i', $row['id']);
            $update_stmt->execute();
            $update_stmt->close();
        } else {
            // Insert new record
            $insert_query = "INSERT INTO stream_stats 
                           (match_id, views, created_at) 
                           VALUES (?, 1, CURDATE())";
            
            $insert_stmt = $conn->prepare($insert_query);
            if ($insert_stmt === false) {
                error_log("Prepare failed for insert in trackStreamView: " . $conn->error);
                return false;
            }

            $insert_stmt->bind_param('i', $match_id);
            $insert_stmt->execute();
            $insert_stmt->close();
        }
        
        $check_stmt->close();
        return true;
        
    } catch (Exception $e) {
        error_log("Error in trackStreamView: " . $e->getMessage());
        return false;
    }
}

function updateDailyStats($match_id) {
    global $conn;
    
    $today = date('Y-m-d');
    
    // Calculează statisticile pentru ziua curentă
    $stmt = $conn->prepare("
        SELECT 
            COUNT(*) as total_views,
            COUNT(DISTINCT ip_address) as unique_viewers,
            MAX(views) as peak_viewers,
            AVG(UNIX_TIMESTAMP(last_view_time) - UNIX_TIMESTAMP(start_time)) as avg_watch_time
        FROM stream_stats 
        WHERE match_id = ? AND DATE(created_at) = ?
    ");
    $stmt->bind_param("is", $match_id, $today);
    $stmt->execute();
    $stats = $stmt->get_result()->fetch_assoc();
    
    // Salvează în istoric
    $stmt = $conn->prepare("
        INSERT INTO stats_history 
        (match_id, date, total_views, unique_viewers, peak_viewers, avg_watch_time)
        VALUES (?, ?, ?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE
            total_views = VALUES(total_views),
            unique_viewers = VALUES(unique_viewers),
            peak_viewers = VALUES(peak_viewers),
            avg_watch_time = VALUES(avg_watch_time)
    ");
    $stmt->bind_param("isiiii", 
        $match_id, 
        $today,
        $stats['total_views'],
        $stats['unique_viewers'],
        $stats['peak_viewers'],
        $stats['avg_watch_time']
    );
    $stmt->execute();
}

function get_browser_name($user_agent) {
    if (strpos($user_agent, 'Opera') || strpos($user_agent, 'OPR/')) return 'Opera';
    elseif (strpos($user_agent, 'Edge')) return 'Edge';
    elseif (strpos($user_agent, 'Chrome')) return 'Chrome';
    elseif (strpos($user_agent, 'Safari')) return 'Safari';
    elseif (strpos($user_agent, 'Firefox')) return 'Firefox';
    elseif (strpos($user_agent, 'MSIE') || strpos($user_agent, 'Trident/7')) return 'Internet Explorer';
    return 'Other';
}

function get_os($user_agent) {
    $os_array = [
        '/windows nt 10/i'      =>  'Windows 10',
        '/windows nt 6.3/i'     =>  'Windows 8.1',
        '/windows nt 6.2/i'     =>  'Windows 8',
        '/windows nt 6.1/i'     =>  'Windows 7',
        '/windows nt 6.0/i'     =>  'Windows Vista',
        '/windows nt 5.2/i'     =>  'Windows Server 2003/XP x64',
        '/windows nt 5.1/i'     =>  'Windows XP',
        '/windows xp/i'         =>  'Windows XP',
        '/windows nt 5.0/i'     =>  'Windows 2000',
        '/windows me/i'         =>  'Windows ME',
        '/win98/i'             =>  'Windows 98',
        '/win95/i'             =>  'Windows 95',
        '/win16/i'             =>  'Windows 3.11',
        '/macintosh|mac os x/i' =>  'Mac OS X',
        '/mac_powerpc/i'       =>  'Mac OS 9',
        '/linux/i'             =>  'Linux',
        '/ubuntu/i'            =>  'Ubuntu',
        '/iphone/i'            =>  'iPhone',
        '/ipod/i'              =>  'iPod',
        '/ipad/i'              =>  'iPad',
        '/android/i'           =>  'Android',
        '/blackberry/i'        =>  'BlackBerry',
        '/webos/i'             =>  'Mobile'
    ];

    foreach ($os_array as $regex => $value) {
        if (preg_match($regex, $user_agent)) {
            return $value;
        }
    }
    return 'Unknown OS';
}

function trackUserAction($action_type, $details = '', $match_id = null) {
    global $conn;
    
    // Nu urmări acțiunile din admin
    if (strpos($_SERVER['REQUEST_URI'], '/admin/') !== false) {
        return;
    }
    
    $session_id = session_id();
    $page_url = $_SERVER['REQUEST_URI'];
    
    $stmt = $conn->prepare("INSERT INTO user_actions (session_id, action_type, action_details, page_url, match_id) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssi", $session_id, $action_type, $details, $page_url, $match_id);
    $stmt->execute();
}

// Apelează trackPageView doar dacă nu suntem în admin
if (strpos($_SERVER['REQUEST_URI'], '/admin/') === false) {
    trackPageView();
} 