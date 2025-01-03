<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Arkafe | The Worksmart Architecture Studio Organizer</title>
    <link rel="icon" href="<?= BASE_URL ?>images/favicon.png">

    <meta name="description" content="The Worksmart Studio Organizer">

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
    <meta property="og:locale" content="en_US">
    <meta property="og:type" content="website">
    <meta property="og:title" content="The Worksmart Architecture Studio Organizer">
    <meta property="og:site_name" content="Arkafe">
    <meta property="og:description" content="Arkafe is a management tool for Architecture, Interior and other Design consultancy Studios.">
    <meta property="og:image:secure_url" content="<?= BASE_URL ?>images/arkafe-logo-box.jpg">
    <meta property="og:url:secure_url" content="<?= BASE_URL ?>">
    <!-- Social Media | Twitter -->
    <meta name="twitter:card"
        content="Arkafe is a management tool for Architecture, Interior and other Design consultancy Studios.">
    <meta name="twitter:image:alt" content="Arkafe">
    <!-- Social Media | Apple & Whatsapp -->
    <link rel="apple-touch-icon" href="<?= BASE_URL ?>images/arkafe-logo-box.jpg">

    <link href="box/style.css" rel="stylesheet" type="text/css">
    <script>
        const apiUrl = "<?= BASE_URL ?>";

        function e$(eid) {
            return document.getElementById(eid)
        }

        /*
        +---------------------------------------------------------------------------+
        | https://developer.mozilla.org/en-US/docs/Web/API/Fetch_API/Using_Fetch    |
        +---------------------------------------------------------------------------+
        | Example POST method implementation:                                       |
        +---------------------------------------------------------------------------+
        */
        async function bdPostData(url = "", formData = {}) {
            // Default options are marked with *
            const response = await fetch(url, {
                method: "POST", // *GET, POST, PUT, DELETE, etc.
                mode: "cors", // no-cors, *cors, same-origin
                cache: "no-cache", // *default, no-cache, reload, force-cache, only-if-cached
                credentials: "same-origin", // include, *same-origin, omit
                // headers: {
                // "Content-Type": "application/json",
                // 'Content-Type': 'application/x-www-form-urlencoded',
                // },
                redirect: "error", // manual, *follow, error
                referrerPolicy: "no-referrer", // no-referrer, *no-referrer-when-downgrade, origin, origin-when-cross-origin, same-origin, strict-origin, strict-origin-when-cross-origin, unsafe-url
                //body: JSON.stringify(data), // body data type must match "Content-Type" header
                body: formData, // RD - use FormData
            });
            return response.json(); // parses JSON response into native JavaScript objects
        }
    </script>
</head>

<body>
    <div class="rd-banner">
        <h1 class="tagline">
            <b>Arkafe</b> The&nbsp;Worksmart&nbsp;Studio&nbsp;Organizer
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