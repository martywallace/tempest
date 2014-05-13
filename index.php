<?php

foreach(array('functions', 'autoloader') as $common)
{
	// Include base requirements.
	require_once __DIR__ . DIRECTORY_SEPARATOR . 'server' . DIRECTORY_SEPARATOR . 'common' . DIRECTORY_SEPARATOR . "$common.php";
}


define("RGX_PATH_DELIMITER", '/[\/\\\\]+/');

define("APP_ROOT", path_normalize(__DIR__, DIRECTORY_SEPARATOR, false, true));
define("PUBLIC_ROOT", path_normalize(dirname($_SERVER["PHP_SELF"]), '/'));
define("DIR_SERVER", path_normalize(__DIR__ . '/server/', DIRECTORY_SEPARATOR, false, true));
define("DIR_STATIC", path_normalize(PUBLIC_ROOT . '/static/', '/'));
define("REQUEST_CLEAN", preg_replace('/(\?|#)(.+)/', '', $_SERVER["REQUEST_URI"]));
define("REQUEST_URI", path_normalize(REQUEST_CLEAN, '/', true, false));
define("APP_REQUEST_URI", path_normalize(PUBLIC_ROOT !== '/' ? str_needle_remove(PUBLIC_ROOT, REQUEST_CLEAN) : REQUEST_URI, '/', true, false));


App\Application::init();