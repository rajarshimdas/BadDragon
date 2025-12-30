<?php

$data['title']                  = 'New Year Offers';
$data['og_locale']              = 'en_US';
$data['og_type']                = 'website';
$data['og_title']               = 'The Worksmart Architecture Studio Organizer';
$data['og_site_name']           = 'Arkafe';
$data['og_description']         = 'New Year Offers';
$data['og_image_secure_url']    = '<?= BASE_URL ?>images/arkafe-offers.png';
$data['og_url_secure_url']      = '<?= BASE_URL ?>';
$data['twitter_card']           = 'Arkafe';
$data['twitter_image_alt']      = 'Arkafe';
$data['apple_touch_icon']       = '<?= BASE_URL ?>images/arkafe-offers.png';

view($route, "offers2026", $data);
