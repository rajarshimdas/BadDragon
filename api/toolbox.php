<?php


$date = new DateTime();
$date->setTimezone(new DateTimeZone('Asia/Kolkata'));

function date_unix2mysql($timestamp) {
    // return 'test';
    return strftime('%Y-%m-%d %H:%M:%S', $timestamp);
}

function view($path, $appFolderPath){
    require $appFolderPath.'/view/'.$path.'.php';
}