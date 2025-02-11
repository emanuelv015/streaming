<?php
session_start();
require_once '../inc/config.php';
require_once '../inc/db_config.php';
require_once '../inc/slug_generator.php';
require_once 'admin_auth.php';

// Verifică autentificarea
requireAdminLogin();

// Add slug column to matches table if it doesn't exist
$query = "SHOW COLUMNS FROM matches LIKE 'slug'";
$result = $conn->query($query);

if ($result->num_rows === 0) {
    $query = "ALTER TABLE matches ADD COLUMN slug VARCHAR(255) AFTER id";
    if ($conn->query($query)) {
        echo "Added slug column to matches table.<br>";
    } else {
        echo "Error adding slug column: " . $conn->error . "<br>";
    }
    
    // Add unique index on slug
    $query = "ALTER TABLE matches ADD UNIQUE INDEX idx_slug (slug)";
    if ($conn->query($query)) {
        echo "Added unique index on slug column.<br>";
    } else {
        echo "Error adding unique index: " . $conn->error . "<br>";
    }
}

// Generate slugs for existing matches
$query = "SELECT id, home_team_id, away_team_id FROM matches WHERE slug IS NULL OR slug = ''";
$result = $conn->query($query);

if ($result->num_rows > 0) {
    while ($match = $result->fetch_assoc()) {
        // Get team names
        $home_query = "SELECT name FROM teams WHERE id = ?";
        $stmt = $conn->prepare($home_query);
        $stmt->bind_param("i", $match['home_team_id']);
        $stmt->execute();
        $home_result = $stmt->get_result();
        $home_team = $home_result->fetch_assoc();
        
        $away_query = "SELECT name FROM teams WHERE id = ?";
        $stmt = $conn->prepare($away_query);
        $stmt->bind_param("i", $match['away_team_id']);
        $stmt->execute();
        $away_result = $stmt->get_result();
        $away_team = $away_result->fetch_assoc();
        
        // Generate title for slug
        $title = $home_team['name'] . ' vs ' . $away_team['name'];
        
        // Generate unique slug
        $slug = generateUniqueSlug($conn, $title, 'matches', $match['id']);
        
        // Update match with new slug
        $update_query = "UPDATE matches SET slug = ? WHERE id = ?";
        $stmt = $conn->prepare($update_query);
        $stmt->bind_param("si", $slug, $match['id']);
        
        if ($stmt->execute()) {
            echo "Updated match ID {$match['id']} with slug: {$slug}<br>";
        } else {
            echo "Error updating match ID {$match['id']}: " . $stmt->error . "<br>";
        }
    }
    echo "All matches have been updated with slugs.<br>";
} else {
    echo "No matches need slug updates.<br>";
}

echo "<br><a href='matches.php'>Return to Matches</a>";
?>
