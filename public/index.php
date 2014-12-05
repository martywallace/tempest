<?php

session_start();
error_reporting(-1);

define('TEMPEST_VERSION', 'v1.0.0');

define('DIR', __DIR__);
define('SEP', DIRECTORY_SEPARATOR);
define('APP_ROOT', DIR . SEP . '../');

define('GET', 'get');
define('POST', 'post');
define('NAMED', 'named');

define('HOST', $_SERVER['HTTP_HOST']);

define('RGX_PATH_DELIMITER', '/[\/\\\\]+/');
define('RGX_TEMPLATE_TOKEN', '/\{\{\s*([\!\?\*]*)(@\w+)*([\w\.\(\)]+)([\w\s\:]*)\s*\}\}/');


foreach(array('functions', 'autoloader') as $inc)
	require_once APP_ROOT . 'server' . SEP . 'common' . SEP . "$inc.php";


define('PUB_ROOT', path_normalize(dirname($_SERVER["PHP_SELF"]), '/', true, true));
define('REQUEST_CLEAN', preg_replace('/(\?|#)(.+)/', '', $_SERVER["REQUEST_URI"]));
define('REQUEST_URI', path_normalize(REQUEST_CLEAN, '/', true, false));
define('APP_REQUEST_URI', path_normalize(PUB_ROOT !== '/' ? str_replace(PUB_ROOT, '', REQUEST_CLEAN) : REQUEST_URI, '/', true, false));


$app = new App();

set_error_handler(array($app, 'error'));

$app->start();