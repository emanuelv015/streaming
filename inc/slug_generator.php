<?php
function generateSlug($string) {
    // Replace non-alphanumeric characters with a dash
    $string = preg_replace('/[^\p{L}\p{N}]+/u', '-', $string);
    
    // Convert to lowercase
    $string = mb_strtolower($string, 'UTF-8');
    
    // Remove dashes from start and end
    $string = trim($string, '-');
    
    // Transliterate (convert accented characters)
    $string = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $string);
    
    // Remove any remaining non-alphanumeric characters
    $string = preg_replace('/[^a-z0-9-]/', '', $string);
    
    return $string;
}

function generateUniqueSlug($conn, $title, $table = 'matches', $id = null) {
    $baseSlug = generateSlug($title);
    $slug = $baseSlug;
    $counter = 1;
    
    // Check if slug exists
    while (true) {
        $query = "SELECT id FROM $table WHERE slug = ? AND id != ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("si", $slug, $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            break;
        }
        
        $slug = $baseSlug . '-' . $counter;
        $counter++;
    }
    
    return $slug;
}
?>
