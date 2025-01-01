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
    die("System Error :: Paths not defined.");

// Wake BadDragon
$wakeUp = BD . '/BadDragon.php';
if (is_file($wakeUp))
    require_once $wakeUp;
else
    die("BadDragon missing...");

// All done. Die in peace...
die();
