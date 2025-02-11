    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Watch live sports online in HD quality. Free sports streams for football, basketball, tennis and more." />
    <meta name="keywords" content="live sports, live streaming, <?php echo $page; ?>" />
    <meta name="author" content="getsportnews.uk" />
    <title><?php echo isset($page_title) ? $page_title . ' - ' . $site : $site; ?></title>
    <meta name="robots" content="index, follow" />
    <meta property="og:title" content="<?php echo $page; ?> | <?php echo $site; ?>">
    <meta property="og:description" content="<?php echo $texts; ?>">
    <meta property="og:url" content="<?php echo $base; ?>/<?php echo $img; ?>">
    <meta property="og:site_name" content="<?php echo $site_name; ?>">
    <meta property="og:image" content="<?php echo $base; ?>/images/img.png"/>
    <meta property="og:locale" content="<?php echo $meta_locale; ?>">
    <meta property="og:type" content="<?php echo $meta_type; ?>">
    <meta property="fb:app_id" content="">
    <meta property="fb:admins" content="">
    <meta name="twitter:title" content="<?php echo $page; ?> | <?php echo $site; ?>">
    <meta name="twitter:description" content="<?php echo $texts; ?>">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:site" content="@">
    <meta name="twitter:creator" content="@">
    <meta name="twitter:image" content="<?php echo $img; ?>"/>
    <meta name="msapplication-TileColor" content="#e9c804">
    <meta name="google-site-verification" content=""/>
    <!-- Favicons -->
    <link rel="shortcut icon" type="image/x-icon" href="/streaming/streamthunder-demo-website/images/favicon/favicon.ico">
    <link rel="icon" type="image/png" href="/streaming/streamthunder-demo-website/images/favicon/favicon-32x32.png">
    <link rel="apple-touch-icon" href="/streaming/streamthunder-demo-website/images/favicon/apple-touch-icon.png">
    <link rel="canonical" href="<?php echo $base; ?>/<?php echo $tag; ?>"/>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="/streaming/streamthunder-demo-website/css/style.css">
    <style>
        .sports_menu {
            display: flex;
            list-style: none;
            padding: 0;
            margin: 0;
            background: #1a1a1a;
            align-items: center;
        }
        
        .sports_menu li {
            padding: 15px;
        }
        
        .sports_menu li a {
            color: #fff;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: color 0.3s;
        }
        
        .sports_menu li a:hover {
            color: #ff5529;
        }
        
        .sports_menu li a.active {
            color: #ff5529;
        }
        
        .sports_menu .logo img {
            height: 40px;
        }
        
        .name {
            font-size: 14px;
        }
        
        .fas {
            font-size: 18px;
        }
        
        ul.nav.navbar-nav.sports_menu {
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
    </style>
    <script type="text/javascript" src="<?php echo $base; ?>/js/jquery.js"></script>
    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
    <script type="text/javascript" src="<?php echo $base; ?>/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="<?php echo $base; ?>/js/cs.js"></script>
 
   
 
<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-104887638-2"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'UA-104887638-2');
</script>

<script>
$( document ).ready(function() {
    $('#aff').affix({
   offset: {
     top: 110,
     bottom: function () {
       return (this.bottom = $('.footer').outerHeight(true))
     }
   }
 });
 });
 
 $(window).scroll(function() {
     var height = $(window).scrollTop();
     if (height > 100) {
         $('#btt').fadeIn();
     } else {
         $('#btt').fadeOut();
     }
 });
 $(document).ready(function() {
     $("#back2Top").click(function(event) {
         event.preventDefault();
         $("html, body").animate({ scrollTop: 0 }, "slow");
         return false;
     });
 
 });
 </script>
