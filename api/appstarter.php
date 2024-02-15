<?php


$date = new DateTime();
$date->setTimezone(new DateTimeZone('Asia/Kolkata'));

function date_unix2mysql($timestamp) {
    // return 'test';
    return date('%Y-%m-%d %H:%M:%S', $timestamp);
}

function view($path, $data){
    require $data['appFolderPath'].'/view/'.$path.'.php';
}