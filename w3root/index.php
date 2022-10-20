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

// Set Paths
$apiFolderPath = '../api';
$appFolderPath = '../app';

// Initialize
require_once($apiFolderPath . '/bootstrap.php');

// Fire the Engine and Take-off
require_once($appFolderPath . '/controller/' . $a . '.php');
