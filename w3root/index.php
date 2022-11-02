<?php /*
+-------------------------------------------------------+
| Rajarshi Das						                    |
+-------------------------------------------------------+
| Created On: 16-Oct-2022				                |
| Updated On: 				                            |
+-------------------------------------------------------+
| Appengine | Front Controller                          |
+-------------------------------------------------------+
*/

// Align to runway
$apiFolderPath = '../api';
$appFolderPath = '../app';

// Fire the Engines
require_once($apiFolderPath . '/bootstrap.php');

// Take-off
require_once($appFolderPath . '/controller/' . $a . '.php');
