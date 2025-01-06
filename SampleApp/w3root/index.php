<?php /* Front Controller
+-------------------------------------------------------+
| Rajarshi Das						                    |
+-------------------------------------------------------+
| Created On:   31-Dec-2024                             |
| Updated On:                                           |
+-------------------------------------------------------+
*/

// Bootstrap
$paths = $_SERVER["DOCUMENT_ROOT"] . '/paths.php';
if (is_file($paths))
    require_once $paths;
else
    die("BadDragon :: Paths not defined.");

// Invoke BadDragon
$BadDragon = BD . '/spitFire.php';
if (is_file($BadDragon))
    // Spit Fire
    require_once $BadDragon;
else
    // BadDragon missing
    echo "Hello BadDragon?! Are you there...";

// All done. Die in peace...
die();
