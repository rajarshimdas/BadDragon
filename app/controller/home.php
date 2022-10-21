<?php

$data = [
    'appFolderPath' => $appFolderPath,
    'page_title' => 'Digital transformation for Architects'
];

view('module/header', $data);
view('module/banner', $data);

// Body
view('home', $data);

view('module/footer', $data);
