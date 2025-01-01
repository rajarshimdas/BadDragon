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
bdLogInFile('Trial', $message, 'trial.log');

// sleep(1);

rdReturnJsonHttpResponse(
    '200',
    ['T', "<h2>Welcome $name,</h2><p>Your trial details will be sent to $email</p>"]
);
