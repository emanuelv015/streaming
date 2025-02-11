<?php
// No direct output before head
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($match_title) ? htmlspecialchars($match_title) . ' - Live Stream' : 'Live Football Streaming'; ?></title>
    <link rel="apple-touch-icon" sizes="180x180" href="<?php echo get_url(); ?>images/favicon/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="<?php echo get_url(); ?>images/favicon/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="<?php echo get_url(); ?>images/favicon/favicon-16x16.png">
    <link rel="manifest" href="<?php echo get_url(); ?>images/favicon/site.webmanifest">
    <link rel="shortcut icon" href="<?php echo get_url(); ?>images/favicon/favicon.ico">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script>
        // Disable right click
        document.addEventListener('contextmenu', function(e) {
            e.preventDefault();
            return false;
        });

        // Disable keyboard shortcuts
        document.addEventListener('keydown', function(e) {
            // Disable F12
            if(e.keyCode == 123) {
                e.preventDefault();
                return false;
            }
            // Disable Ctrl+Shift+I, Ctrl+Shift+J, Ctrl+U
            if(e.ctrlKey && e.shiftKey && (e.keyCode === 73 || e.keyCode === 74 || e.keyCode === 85)) {
                e.preventDefault();
                return false;
            }
            // Disable Ctrl+S
            if(e.ctrlKey && e.keyCode === 83) {
                e.preventDefault();
                return false;
            }
        });

        // Disable text selection
        document.addEventListener('selectstart', function(e) {
            e.preventDefault();
            return false;
        });
    </script>
</head>
<body>
