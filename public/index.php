<?php

session_start();
error_reporting(-1);

define('TEMPEST_VERSION', 'v1.0.0');

define('DIR', __DIR__);
define('SEP', DIRECTORY_SEPARATOR);
define('APP_ROOT', DIR . '/../');

define('GET', 'get');
define('POST', 'post');
define('NAMED', 'named');

define('HOST', $_SERVER['HTTP_HOST']);

define('RGX_PATH_DELIMITER', '/[\/\\\\]+/');
define('RGX_TEMPLATE_TOKEN', '/\{\{\s*([\!\?\*]*)(@\w+)*([\w\.\(\)]+)([\w\s\:]*)\s*\}\}/');


require_once APP_ROOT . 'server/tempest/functions.php';
require_once APP_ROOT . 'server/tempest/autoloader.php';
require_once APP_ROOT . 'server/vendor/autoload.php';


define('PUB_ROOT', path_normalize(dirname($_SERVER["PHP_SELF"]), '/', true, true));
define('REQUEST_CLEAN', preg_replace('/(\?|#)(.+)/', '', $_SERVER["REQUEST_URI"]));
define('REQUEST_URI', path_normalize(REQUEST_CLEAN, '/', true, false));
define('APP_REQUEST_URI', path_normalize(PUB_ROOT !== '/' ? str_replace(PUB_ROOT, '', REQUEST_CLEAN) : REQUEST_URI, '/', true, false));


$app = new App();

set_error_handler(array($app, 'error'));

/**
 * Returns the active application instance, or a service that was added to that it.
 *
 * @param string $service The service name.
 *
 * @return App
 */
function tempest()
{
	return App::getInstance();
}

$app->start();