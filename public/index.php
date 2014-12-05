<?php

session_start();
error_reporting(-1);

define('TEMPEST_VERSION', 'v1.0.0');

define('DIR', __DIR__);
define('APP_ROOT', DIR . '/../');
define('HOST', $_SERVER['HTTP_HOST']);

define('GET', 'get');
define('POST', 'post');
define('NAMED', 'named');

define('REQUEST_CLEAN', preg_replace('/(\?|#)(.+)/', '', $_SERVER["REQUEST_URI"]));


require_once APP_ROOT . 'server/tempest/functions.php';
require_once APP_ROOT . 'server/tempest/autoload.php';
require_once APP_ROOT . 'server/vendor/autoload.php';


define('REQUEST_URI', path_normalize(REQUEST_CLEAN, '/', true, false));


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