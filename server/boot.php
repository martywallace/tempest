<?php

define('APP_ROOT', realpath(__DIR__ . '/../') . '/');

require_once APP_ROOT . 'server/tempest/autoload.php';
require_once APP_ROOT . 'server/vendor/autoload.php';
require_once APP_ROOT . 'server/tempest/functions.php';

use Tempest\Utils\Path;


define('GET', 'get');
define('POST', 'post');
define('NAMED', 'named');

define('HOST', strtolower($_SERVER['SERVER_NAME']));
define('REQUEST_CLEAN', preg_replace('/(\?|#)(.*)/', '', $_SERVER["REQUEST_URI"]));
define('REQUEST_URI', Path::create(REQUEST_CLEAN, Path::DELIMITER_LEFT));