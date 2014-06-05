<?php

define("DIR", __DIR__);
define("SEP", DIRECTORY_SEPARATOR);

define('GET', 'get');
define('POST', 'post');
define('NAMED', 'named');

define("RGX_PATH_DELIMITER", '/[\/\\\\]+/');
define("RGX_TEMPLATE_TOKEN", '/\{{2}\s*([!\?]*)(\@\w+)*([\w\.\(\)]+)([\w\s\:]+)\s*\}{2}/');


foreach(array('functions', 'autoloader') as $inc) require_once DIR . SEP . 'server' . SEP . 'common' . SEP . "$inc.php";


define("APP_ROOT", path_normalize(DIR, SEP, true, true));
define("PUB_ROOT", path_normalize(dirname($_SERVER["PHP_SELF"]), '/'));
define("DIR_SERVER", path_normalize(DIR . '/server/', SEP, false, true));
define("DIR_STATIC", path_normalize(DIR . '/static/', SEP, false, true));
define("PUB_STATIC", path_normalize(PUB_ROOT . '/static/', '/'));
define("REQUEST_CLEAN", preg_replace('/(\?|#)(.+)/', '', $_SERVER["REQUEST_URI"]));
define("REQUEST_URI", path_normalize(REQUEST_CLEAN, '/', true, false));
define("APP_REQUEST_URI", path_normalize(PUB_ROOT !== '/' ? str_replace(PUB_ROOT, '', REQUEST_CLEAN) : REQUEST_URI, '/', true, false));


$app = new Application();

set_error_handler(array($app, 'error'));

$app->start();