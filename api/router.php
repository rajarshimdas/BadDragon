<?php /* Router
+-------------------------------------------------------+
| Rajarshi Das						                    |
+-------------------------------------------------------+
| Created On: 16-Oct-2022				                |
| Updated On: 				                            |
+-------------------------------------------------------+
| URI                                                   |
| https://domain.tld/app/controller/method/variable     |
| https://domain.tld/rolodex/controller/method/...      |
+-------------------------------------------------------+
| Nginx Rewrite Rules                                   |
| location / {                                          |
|    try_files $uri /index.php$is_args$args;            |
| }                                                     |
+-------------------------------------------------------+
*/

// var_dump($_SERVER);
// echo '<br>Request URI: ' . $_SERVER['REQUEST_URI'];

if (filter_has_var(INPUT_POST, 'a')) {

    // POST

    // Controller 
    $a = $_POST['a'];

    // Method
    $m = $_POST['m'];
    
} else {

    // GET

    // Get the Parts of URI
    // Request URI: /rolodex/index.php/test
    // Request URI: /rolodex/test
    $uri_request = explode('/', $_SERVER['REQUEST_URI']);
    // var_dump($uri_request);
    $uri_co = count($uri_request);

    // Controller Index
    $uri_controller_index = 1;
    if ($uri_request[$uri_controller_index] === 'index.php') {
        $uri_controller_index++;
    }

    // Controller
    if (count($uri_request) > $uri_controller_index) {
        $a = $uri_request[$uri_controller_index];
    }

    // Method Index
    $uri_method_index = $uri_controller_index + 1;
    if ($uri_co > $uri_method_index) {
        $m = $uri_request[$uri_method_index];
    }

    // if no controller specified
    if (!$a) $a = 'home';

    // if no method specified
    if (!$m) $m = 'x';
}


// Load custom routes
// Todo




// replace - with _
$a = str_replace('-', '_', $a);
$m = str_replace('-', '_', $m);


// Sanitize $a and $b



// $options = array($regexes['alfanum']);
$flag = array(
    "options" => array(
        // "regexp" => "/^[0-9a-zA-Z_]+$/"
        "regexp" => $regexp['uri']
    )
);
// var_dump($flag);

// Validate controller
if (!validate_regex($a, $flag)) {
    die('validation failed: ' . $a);
}

// Validate method
if (!validate_regex($m, $flag)) {
    die('validation failed: ' . $m);
}

// Check if controller is available 
if (!file_exists($appFolderPath . '/controller/' . $a . '.php')) {
    die('Controller not found');
}

// Debug 
if ($debug_flag > 0) {
    echo '<div>Request URI: ' . $_SERVER['REQUEST_URI'] . '</div>';
    echo '<div>Controller: ' . $a . '</div>';
}
