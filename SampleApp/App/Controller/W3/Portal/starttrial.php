<?php
/*
+-------------------------------------------------------+
| Rajarshi Das						                    |
+-------------------------------------------------------+
| Created On:   25-Sep-2024                             |
| Updated On:                                           |
+-------------------------------------------------------+
*/
$name = trim($_POST["name"]);
$email = trim($_POST["email"]);
$website = trim($_POST["website"]);

/*
+-------------------------------------------------------+
| Data Validation                                       |
+-------------------------------------------------------+
*/
require_once W3APP . '/Controller/validation.php';

$errorFlag = 0;
$errorMsg = "<!-- Error -->";

if (!min_length($name, 1)) {
    $errorMsg = $errorMsg . "<p>Name is required.</p>";
    $errorFlag = 1;
} 

if (!valid_email($email)) {
    $errorMsg = $errorMsg . "<p>Email $email is not valid. Please enter a valid email.</p>";
    $errorFlag = 1;
}

if (!min_length($website, 1)) {
    $errorMsg = $errorMsg . "<p>Website URL is required.</p>";
    $errorFlag = 1;
} 

if ($errorFlag > 0) {

    rdReturnJsonHttpResponse(
        '200',
        ["F", $errorMsg]
    );
}

/*
+-------------------------------------------------------+
| Log message to file                                   |
+-------------------------------------------------------+
*/
$message = "$name | $email | $website";
log2file('Trial', $message);

sleep(1);

rdReturnJsonHttpResponse(
    '200',
    ['T', "<h2>Welcome $name,</h2><p>Your trial details will be sent to $email</p>"]
);



function log2file($status, $message)
{

    $dt = date("Y-m-d");
    $tm = date("H:i:s");

    $post = ($status == "F") ? json_encode($_POST) : 'ok';

    $log = "$status | $dt | $tm | REMOTE_ADDR: " . $_SERVER["REMOTE_ADDR"] . " [ M: " . $message . " ] " . $_SERVER["HTTP_USER_AGENT"];
    $logfile = FILEDB . "/log/trial.log";

    if (!is_file($logfile)){
        rdReturnJsonHttpResponse(
            '200',
            ["F", "Logfile not found."]
        );
    }
    
    // Open/Create the logfile
    $f = fopen($logfile, "a");
    fwrite($f, $log . "\n");
    fclose($f);
}
