<?php

// CONSTANTS //
// 
// Defines Directory Seperator //
defined('DS') ? null : define('DS', '/');
// Defines Site path //
defined('SITE_ROOT') ? null : define('SITE_ROOT', dirname(__FILE__) . '/../../motivator');
// Defines the system folder where the application lives //
defined('SYSTEM') ? null : define('SYSTEM', SITE_ROOT . DS . 'system');
// Defines the images directory //
defined('IMAGE_DIR') ? null : define('IMAGE_DIR', SITE_ROOT . DS . 'public' . DS . 'images');
// Defines the includes directory //
defined('INCLUDES') ? null : define('INCLUDES', SYSTEM . DS . 'includes');
// Defines the Fonts directory //
defined('FONTS_DIR') ? null : define('FONTS_DIR', INCLUDES . DS . 'fonts');

// Require functions and router //
require_once(SYSTEM . DS . 'includes' . DS . 'functions.php');
require_once(SYSTEM . DS . 'includes' . DS . 'router.php');

// Initialize the router //
$router = new Router();

// Load the selected route //
$router->load();
?>