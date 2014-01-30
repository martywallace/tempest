<?php

// Tempest PHP framework.
// Author: Marty Wallace.
// https://github.com/MartyWallace/Tempest
session_start();


// Global constants.
define('PATTERN_SLASHES', '/[\\\|\/]+/');
define('GET', 'get');
define('POST', 'post');
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
define('SERVER_ROOT', normalizePath($_SERVER["DOCUMENT_ROOT"]));
define('CLIENT_ROOT', normalizePath(str_replace(SERVER_ROOT, '', __DIR__), '/') . '/');
define('APP_ROOT', normalizePath(SERVER_ROOT . CLIENT_ROOT));
define('REQUEST_METHOD', strtolower($_SERVER["REQUEST_METHOD"]));
define('REQUEST_URI', cleanUri(str_replace(CLIENT_ROOT, '', $_SERVER["REQUEST_URI"])));


/**
 * Attempt to import a file into the current context.
 * @param $file The file path, name and extension from the "/server/" directory.
 * @return True if the file was found, else false.
 */
function import($file)
{
	$base = APP_ROOT . DIRECTORY_SEPARATOR . 'server' . DIRECTORY_SEPARATOR;
	$applicationPath = "{$base}{$file}";
	$vendorPath = "{$base}vendor" . DIRECTORY_SEPARATOR . "{$file}";

	// Try normal path using full namespace first.
	if(file_exists($applicationPath))
	{
		require_once $applicationPath;
		return true;
	}

	// Look in "/server/vendor/" next.
	else if(file_exists($vendorPath))
	{
		require_once $vendorPath;
		return true;
	}

	else
	{
		// Class could not be loaded.
		die("Class <code>$applicationPath</code> not found.");
	}

	return false;
}


/**
 * Normalize an input path.
 * @param $path The path to normalize.
 * @param $separator The path separator, normally DIRECTORY_SEPARATOR.
 */
function normalizePath($path, $separator = DIRECTORY_SEPARATOR)
{
	$base = preg_replace(PATTERN_SLASHES, $separator, $path);
	$base = rtrim($base, $separator);

	return $base;
}


/**
 * Cleans up a URI string, removing duplicate slashes, query params and trailing hash params.
 * @param $uri The input URI.
 */
function cleanUri($uri)
{
	$base = preg_replace(PATTERN_SLASHES, '/', $uri);
	$base = preg_replace('/[#|\?].*$/', '', $base);

	return $base;
}


// Autoloader.
spl_autoload_register(function($class)
{
	$class = str_replace('\\', DIRECTORY_SEPARATOR, $class);
	$path = "$class.php";

	import($path);

});


// Initialize the core Application.
$application = new \app\Application();