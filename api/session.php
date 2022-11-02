<?php
/*
session_name('CONCERT');
session_start();

// Session Array 
$sx = array (
    $session_id     => session_id(),
    $user_id        => $_SESSION['user_id'],
    $loginname      => $_SESSION['loginname'],
    $fullname       => $_SESSION['fullname']
);

// Check if login has timed out
$now = time();  // Unixtimestamp
$loginexp = $_SESSION['loginexp'];

if ($now > $loginexp) {
    exit('Session timed out.');
}

// Set new loginexp
$_SESSION['loginexp'] = $now + $config['LoginTimeOut'];


// Debug
if ($debug_flag > 0) {
    echo '<div>Session: '.session_id()
        .'<pre>';
    var_dump($_SESSION);
    echo '</pre>now: '.date_unix2mysql($now).' | loginexp: '.date_unix2mysql($_SESSION['loginexp']).'</div?';
}

*/
