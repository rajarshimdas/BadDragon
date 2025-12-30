<!DOCTYPE html>
<html lang="en">
<?php

/*
$title
$og_locale
$og_type
$og_title
$og_site_name
$og_description
$og_image_secure_url
$og_url_secure_url
$twitter_card
$twitter_image_alt
$apple_touch_icon
*/

?>
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Arkafe | <?= $title ?></title>
    <link rel="icon" href="<?= BASE_URL ?>images/favicon.png">

    <meta name="description" content="<?= $og_title ?>">

    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-LMK2PH2RJM"></script>
    <script>
        window.dataLayer = window.dataLayer || [];

        function gtag() {
            dataLayer.push(arguments);
        }
        gtag('js', new Date());
        gtag('config', 'G-LMK2PH2RJM');
    </script>

    <!-- Social Media | Open Graph Protocol -->
    <meta property="og:locale" content="<?= $og_locale ?>">
    <meta property="og:type" content="<?= $og_type ?>">
    <meta property="og:title" content="<?= $og_title ?>">
    <meta property="og:site_name" content="<?= $og_site_name ?>">
    <meta property="og:description" content="<?= $og_description ?>">
    <meta property="og:image:secure_url" content="<?= $og_image_secure_url ?>">
    <meta property="og:url:secure_url" content="<?= $og_url_secure_url ?>">
    <!-- Social Media | Twitter -->
    <meta name="twitter:card"
        content="<?= $twitter_card ?>">
    <meta name="twitter:image:alt" content="<?= $twitter_image_alt ?>">
    <!-- Social Media | Apple & Whatsapp -->
    <link rel="apple-touch-icon" href="<?= $apple_touch_icon ?>">

    <link href="public/BadDragon.css" rel="stylesheet" type="text/css">
    <link href="box/style.css" rel="stylesheet" type="text/css">
    <script>
        const apiUrl = "<?= BASE_URL ?>index.php";
    </script>
    <script src="public/BadDragon.js" type="text/javascript"></script>
</head>

<body>
    <div class="rd-banner">
        <h1 class="tagline">
            <b>Arkafe</b> The&nbsp;Worksmart&nbsp;Architecture&nbsp;Studio&nbsp;Organizer
        </h1>
    </div>

    <?php
    $viewpage = realpath(__DIR__ . "/../" . VIEWPAGE . ".php");
    if (is_file($viewpage)) {
        require_once $viewpage;
    } else {
        die("Viewpage file not found");
    }
    ?>

    <!-- Footer -->
    <div class="rd-footer">
        <p>
            Interested? Reach us at <?= MAILTO ?>
        </p>
    </div>

</body>

</html>