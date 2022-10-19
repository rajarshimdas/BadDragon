<?php

// Read-only
$cn1 = new mysqli($db_host, $db_user1, $db_passwd1, $db_name);
// Write to db
$cn2 = new mysqli($db_host, $db_user2, $db_passwd2, $db_name);

// Debugging
if ($debug_flag > 0) echo '<div>Mysql '.$cn1->server_info.'</div>';
