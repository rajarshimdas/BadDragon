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

## Validation Library
##
require_once __DIR__ . '/Toolbox/Validation.php';
$bdIsValid = new bdDataValidation();


## Session to Individual variables
##
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


## Login (and Logout) log
##
function bdLogInFile(string $status, string $message, string $logfile): bool
{
    $dt = date("Y-m-d");
    $tm = date("H:i:s");
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'CLI';

    $log = "$dt | $tm | $ip | $status [ M: $message ]";

    // Backward compatibility support
    $logdir = defined('FILEDB') ? FILEDB : W3FILEDB;

    // Log file
    $fullPath = rtrim($logdir, '/') . "/log/" . $logfile;

    // Ensure directory exists
    $dir = dirname($fullPath);
    if (!is_dir($dir)) {
        if (!mkdir($dir, 0755, true)) {
            return false;
        }
    }

    // Append log safely with file locking
    return (bool) file_put_contents(
        $fullPath,
        $log . PHP_EOL,
        FILE_APPEND | LOCK_EX
    );

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


function bdWriteActionLog(
    string $flag,
    string $message,
    string $logDir,
    object $route
): void {

    /* Multiple files - not required 
    $moduleDir = rtrim($logDir, '/') . '/' . $route->module . '/' .  $route->controller . '/' . $route->method;
    $logFile   = $moduleDir . '/' . $route->method . '.log';
    */

    $moduleDir = rtrim($logDir, '/');
    $logFile = $moduleDir . DIRECTORY_SEPARATOR . 'cud_' . date('Ym') . '.log';

    // Create directory if it does not exist
    if (!is_dir($moduleDir)) {
        if (!mkdir($moduleDir, 0755, true) && !is_dir($moduleDir)) {
            throw new RuntimeException("Unable to create log directory: {$moduleDir}");
        }
    }

    // Create file if it does not exist
    if (!file_exists($logFile)) {
        if (false === touch($logFile)) {
            throw new RuntimeException("Unable to create log file: {$logFile}");
        }
        chmod($logFile, 0644);
    }

    $name = defined('DISPLAYNAME') ? DISPLAYNAME : 'NA'; // This is not an error

    $entry = sprintf(
        "%s | %s | %s | %s | %s%s",
        $flag,
        date('Y-m-d | H:i:s'),    // Easy to open as CSV and filter date
        $name,
        $route->uri,
        $message,
        PHP_EOL
    );

    file_put_contents($logFile, $entry, FILE_APPEND | LOCK_EX);
}


/*
+-------------------------------------------------------+
| Universal Date Range Splitter                         |
| Supports: month, year, financial year, payroll cycle  |
+-------------------------------------------------------+
*/

function bdSplitDateRangeByMode($sdt, $edt, $mode = 'month', $cycleStartDay = 1)
{
    $blocks = [];

    $period = new DatePeriod(
        new DateTime($sdt),
        new DateInterval('P1D'),
        (new DateTime($edt))->modify('+1 day')
    );

    foreach ($period as $dt) {

        switch ($mode) {

            case 'year':
                $key = $dt->format('Y');
                break;

            case 'fy':  // Financial Year (Apr–Mar)
                $y = $dt->format('Y');
                $m = $dt->format('n');
                $key = ($m < 4) ? ($y - 1) . '-' . $y : $y . '-' . ($y + 1);
                break;

            case 'payroll':

                $day = (int)$dt->format('d');
                $month = (int)$dt->format('m');
                $year = (int)$dt->format('Y');

                if ($day < $cycleStartDay) {
                    $month--;
                    if ($month == 0) {
                        $month = 12;
                        $year--;
                    }
                }

                $key = sprintf('%04d-%02d', $year, $month);
                break;

            case 'month':
            default:
                $key = $dt->format('Y-m');
        }

        if (!isset($blocks[$key])) {
            $blocks[$key] = [
                'sdt' => $dt->format('Y-m-d'),
                'edt' => $dt->format('Y-m-d')
            ];
        } else {
            $blocks[$key]['edt'] = $dt->format('Y-m-d');
        }
    }

    return array_values($blocks);
}
