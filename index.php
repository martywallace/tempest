<?php

namespace tempest;


// Tempest PHP framework.
// Author: Marty Wallace.
// https://github.com/MartyWallace/Tempest
session_start();


// Global constants.
define('PATTERN_SLASHES', '/[\/\\\\]+/');
define('PATTERN_DOTS', '/\.+/');
define('PATTERN_NAMED_ROUTE_PART', '/^\[[^\]]+\]/');
define('PATTERN_TOKEN', '/\{{2}\s?([!\?]*)(\@\w+)*([\w\.\(\)]+)([\w\s\|]+)\s?\}{2}/');

define('GET', 'get');
define('POST', 'post');
define('NAMED', 'named');

define('MIME_TEXT', 'text/plain');
define('MIME_HTML', 'text/html');
define('MIME_JAVASCRIPT', 'text/javascript');
define('MIME_CSS', 'text/css');
define('MIME_JSON', 'application/json');
define('MIME_BINARY', 'application/octet-stream');
define('MIME_ZIP', 'application/zip');
define('MIME_PDF', 'application/pdf');
define('MIME_JPEG', 'image/jpeg');
define('MIME_GIF', 'image/gif');
define('MIME_PNG', 'image/png');

define('APP_ROOT', normalizePath(__DIR__));
define('CLIENT_ROOT', normalizePath(dirname($_SERVER["PHP_SELF"]), '/'));
define('REQUEST_METHOD', strtolower($_SERVER["REQUEST_METHOD"]));
define('REQUEST_URI', cleanUri(str_replace(CLIENT_ROOT, '/', $_SERVER["REQUEST_URI"])));
define('RESPONSE_DIR', 'app\\responses\\');
define('TEMPLATE_DIR', APP_ROOT . 'view' . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR);
define('DEFAULT_RESPONSE_NAME', 'index');


/**
 * Normalize an input path.
 * @param $path The path to normalize.
 * @param $separator The path separator, normally DIRECTORY_SEPARATOR.
 */
function normalizePath($path, $separator = DIRECTORY_SEPARATOR)
{
	if(strlen($path) === 0 || $path === '/' || $path === '\\' || $path === $separator) return $separator;

	$base = preg_replace(PATTERN_SLASHES, $separator, $path);
	$base = rtrim($base, $separator);

	return $base . $separator;
}


/**
 * Cleans up a URI string, removing duplicate slashes, query params and trailing hash params.
 * @param $uri The input URI.
 */
function cleanUri($uri)
{
	$base = preg_replace(PATTERN_SLASHES, '/', $uri);
	$base = preg_replace('/[#|\?].*$/', '', $base);
	$base = trim($base, '/');

	return $base;
}


// Autoloader.
spl_autoload_register(function($class)
{
	$class = str_replace('\\', DIRECTORY_SEPARATOR, $class);
	$applicationPath = APP_ROOT . 'server' . DIRECTORY_SEPARATOR . "$class.php";
	$vendorPath = APP_ROOT . 'vendor' . DIRECTORY_SEPARATOR . "$class.php";

	// Try normal path using full namespace first.
	if(is_file($applicationPath)) require_once $applicationPath;
	else if(is_file($vendorPath)) require_once $vendorPath;
	else trigger_error("Class <code>$applicationPath</code> not found.");

});


// Initialize the core Application.
$errorHandler = new \tempest\base\ErrorHandler();
$application = new \app\Application($errorHandler);