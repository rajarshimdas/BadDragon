<?php

## Pass variables to View
## 
$data['title']                  = 'Grab exciting offers';
$data['og_locale']              = 'en_US';
$data['og_type']                = 'website';
$data['og_title']               = 'Happy New Year';
$data['og_site_name']           = 'Happy New Year';
$data['og_description']         = 'Grab exciting New Year offers';
$data['og_url']                 = BASE_URL;
$data['og_image_secure_url']    = BASE_URL . 'images/arkafe-2026.png';
$data['og_image_type']          = 'image/png';
$data['og_image_width']         = '400';
$data['og_image_height']        = '400';

$data['twitter_card']           = 'Arkafe';
$data['twitter_image_alt']      = 'Arkafe';

$data['apple_touch_icon']       = BASE_URL . 'images/arkafe-2026.png';

## Generate Page
##
view($route, "offers2026", $data);
