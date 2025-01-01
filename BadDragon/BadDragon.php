<?php /* BadDragon 
+-------------------------------------------------------+
| Rajarshi Das						                    |
+-------------------------------------------------------+
| Created On:   29-Jan-2024                             |
| Updated On:                                           |
+-------------------------------------------------------+
*/

// Fetch BadDragon config
require_once BD . "/Config.php";

// Override config vars
$envfile = W3APP . "/env.php";
if (is_file($envfile))
    require_once $envfile;
else
    die("System Error :: ENV file not found.");

// Common Functions
require_once BD . "/Common.php";

// Autoload
if (!defined('BADDRAGON')) {
    require_once BD . '/Autoload.php';
}

// Invoke BadDragon
use BadDragon\Controller;
use BadDragon\Router;
//die("Bd");

$dragon = new Controller;
$route = new Router;
// var_dump($route); die;

// Request controllers
$framework = $dragon->fire($route);

// Load Controllers
for ($i = 0; $i < count($framework); $i++) {
    require_once $framework[$i];
}

// Clean up
if (isset($mysqli)) $mysqli->close();

// Log this request
bdLogInFile('Request', $route->uri, 'access.log');

