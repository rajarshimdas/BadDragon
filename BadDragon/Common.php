<?php /* 
+-------------------------------------------------------+
| Rajarshi Das						                    |
+-------------------------------------------------------+
| Created On: 29-Jan-2024                               |
| Updated On: 31-Dec-2025                               |
+-------------------------------------------------------+
| Provide common functions throughout the framework     |
+-------------------------------------------------------+
*/
require_once __DIR__ . '/Toolbox/Validation.php';
$bdIsValid = new bdDataValidation();

function sessionToVars(array $session)
{
    foreach ($session as $key => $value) {
        if (preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $key)) {
            $GLOBALS[$key] = $value;
        }
    }
}

// session_start();
// sessionToVars($_SESSION);

/*
+-------------------------------------------------------+
| Controller handover to View for generating Page       |
+-------------------------------------------------------+
*/

function view(object $route, string $page, array $data = []): bool
{
    // var_dump($route);
    extract($data);

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
/*
+-------------------------------------------------------+
| System Common Functions                               |
+-------------------------------------------------------+
*/

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
    header("Location:?" . BASE_URL . "$uri");
    die;

    return true;
}

function rx($var)
{
    echo '<pre>', var_dump($var), '</pre>';
}


function rd($var)
{
    echo '<div>' . $var . '</div>';
}

// Return JSON Response | legacy
function rdReturnJsonHttpResponse(string $httpCode, array $data)
{
    // For legacy Apps using it
    die(bdReturnJSON($data, $httpCode));
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
    // die();
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
        return false;
    }

    return true;
}

function bdIsValidDateMySQLFormat(string $date): bool
{
    return checkValidISODate($date);
}


// ChatGPT | 2025-10-27
function checkValidISODate(string $date): bool
{

    // Blank string check
    if (trim($date) === '') {
        return false;
    }

    // Must strictly match YYYY-MM-DD
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
        return false;
    }

    // Check if it’s a real calendar date
    [$year, $month, $day] = explode('-', $date);
    return checkdate((int)$month, (int)$day, (int)$year);
}


## Database connection
##

// Select User
function bdCN1() {

    $host   = DB_HOST;
    $un     = CN1_UNAME;
    $pw     = CN1_PASSWD;
    $db     = DB_NAME;

    $mysqli = new mysqli($host, $un, $pw, $db);
    if (mysqli_connect_errno()) {
        printf("MySQL[cn1]: %s\n", mysqli_connect_error());
        die;
    }
    // printf("MySQL[1]: %s\n", $mysqli->host_info);
    return $mysqli;

}

// Super User
function bdCN2() {

    $host   = DB_HOST;
    $un     = CN2_UNAME;
    $pw     = CN2_PASSWD;
    $db     = DB_NAME;

    $mysqli = new mysqli($host, $un, $pw, $db);
    if (mysqli_connect_errno()) {
        printf("MySQL[cn2]: %s\n", mysqli_connect_error());
        die;
    }
    // printf("MySQL[1]: %s\n", $mysqli->host_info);
    return $mysqli;

}
