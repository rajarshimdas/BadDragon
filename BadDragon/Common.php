<?php /*
+-------------------------------------------------------+
| System Common Functions                               |
+-------------------------------------------------------+
*/

// use BadDragon\Router;

function view($route, $page)
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

function show404($m)
{
    die($m);
}

function alpha_numeric_dash_slash($str)
{
    return (bool) preg_match('/^[a-z0-9-//]+$/i', $str);
}

function bdGo2uri(string $uri): bool
{
    header("Location:?$uri");
    die;

    return true;
}


// Return JSON Response
function rdReturnJsonHttpResponse($httpCode, $data)
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
