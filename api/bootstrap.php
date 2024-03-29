<?php 

// Instantiate config
$config['appFolderPath'] = $appFolderPath;
$config['apiFolderPath'] = $apiFolderPath;

// Read Configuration
require_once ($appFolderPath.'/config.cgi');

// Bootstrap MVC Framework

require_once ($apiFolderPath.'/appstarter.php');
require_once ($apiFolderPath.'/session.php');
require_once ($apiFolderPath.'/database.php');
require_once ($apiFolderPath.'/validation.php');

// Load common functions
require_once ($appFolderPath.'/common.php');

// Call the controller
require_once ($apiFolderPath.'/router.php');
