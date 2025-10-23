<?php /* BadDragon 
+-------------------------------------------------------+
| Rajarshi Das						                    |
+-------------------------------------------------------+
| Created On: 29-Jan-2024                               |
| Updated On: 23-Oct-2025                               |
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
// die(var_dump($route));

// Request controllers
$framework = $dragon->fire($route);
// die(var_dump($framework));

// Load Controllers - base controller
if (is_file(W3APP . "/Controller/Controller.php")) {
    require_once W3APP . "/Controller/Controller.php";
}

// Load Controllers - MVC
// Cast null to an empty array, so the loop runs safely with no warning and no output.
foreach((array)$framework as $controller){
    require_once $controller;
}

// Clean up
if (isset($mysqli)) $mysqli->close();

// Log this request
# $logMessage = empty($logMessage) ? 'BD' : $logMessage;
# bdLogInFile($logMessage, $route->uri, 'access.log');
