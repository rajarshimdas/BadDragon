<?php /*
+-------------------------------------------------------+
| System Common Functions                               |
+-------------------------------------------------------+
*/

// use BadDragon\Router;

function view(object $route, string $page): bool
{
    // var_dump($route);

    $p = W3APP . "/View/" . $route->module . "/" . $route->controller . "/Generate/Page.php";
    // echo $p;

    define("VIEWPAGE", $page);

    if (file_exists($p)) {
        require_once $p;
    } elseif (ENV == "dev") {
        die("View not found: " . $p);
    } else {
        show404("Page not found");
        return false;
    }

    return true;
}

function show404(string $m): bool
{
    die($m);
}

function alpha_numeric_dash_slash(string $str): bool
{
    return (bool) preg_match('/^[a-z0-9\\/-]+$/i', $str);
}

function bdGo2uri(string $uri): bool
{
    header("Location:?$uri");
    die;

    return true;
}


// Return JSON Response | legacy
function rdReturnJsonHttpResponse(string $httpCode, array $data)
{
    // For legacy Apps using it
    bdReturnJSON($data, $httpCode);
}

// Return JSON Response
function bdReturnJSON(array $data, string $httpCode = '200')
{
    // remove any string that could create an invalid JSON 
    // such as PHP Notice, Warning, logs...
    ob_start();
    ob_clean();

    // this will clean up any previously added headers, to start clean
    header_remove();

    // Set the content type to JSON and charset 
    // (charset can be set to something else)
    // add any other header you may need, gzip, auth...
    header("Content-type: application/json; charset=utf-8");

    // Set your HTTP response code, refer to HTTP documentation
    http_response_code($httpCode);

    // encode your PHP Object or Array into a JSON string.
    // stdClass or array
    echo json_encode($data);

    // making sure nothing is added
    die();
}


function bdLogInFile(string $status, string $message, string $logfile): bool
{

    $dt = date("Y-m-d");
    $tm = date("H:i:s");

    $log = "$dt | $tm | " . $_SERVER["REMOTE_ADDR"] . " | $status [ M: " . $message . " ]";
    $logfile = FILEDB . "/log/$logfile";

    if (is_file($logfile)) {

        // Open/Create the logfile
        $f = fopen($logfile, "a");
        fwrite($f, $log . "\n");
        fclose($f);
    } else {

        // Logfile not found
        rdReturnJsonHttpResponse(
            '200',
            ["F", "Logfile not found. $logfile"]
        );
    }
    return true;
}

function bdIsValidDateMySQLFormat(string $date): bool
{
    if (empty($date)) return false;

    $x = explode("-", $date);

    $co = isset($x) ? count($x) : 0;
    if ($co == 3) {
        $dtD = (int)$x[2];
        $dtM = (int)$x[1];
        $dtY = (int)$x[0];
    }

    if (!is_int($dtM) || empty($dtM) || $dtM < 1 || $dtM > 12) return false;
    if (!is_int($dtY) || empty($dtY) || $dtY < 1977 || $dtY > 3000) return false;

    // Check last day of month | Todo
    $noOfDays = date('t', strtotime($dtY . '-' . $dtM . '-01'));
    //echo $noOfDays;
    if (!is_int($dtD) || empty($dtD) || $dtD < 1 || $dtD > $noOfDays) return false;
    
    // Ok
    return true;
}

