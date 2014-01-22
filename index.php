<?php

// Global constants.
define('GET', 'get');
define('POST', 'post');
define('MIME_TEXT', 'text/plain');
define('MIME_HTML', 'text/html');
define('MIME_JAVASCRIPT', 'text/javascript');
define('MIME_CSS', 'text/css');
define('MIME_JSON', 'application/json');
define('MIME_BINARY', 'application/octet-stream');
define('MIME_ZIP', 'application/zip');
define('MIME_JPEG', 'image/jpeg');
define('MIME_GIF', 'image/gif');
define('MIME_PNG', 'image/png');
define('REQUEST_METHOD', strtolower($_SERVER["REQUEST_METHOD"]));
define('REQUEST_URI', strtolower($_SERVER["REQUEST_URI"]));



/**
 * Attempt to import a file into the current context.
 * @param $file The file path, name and extension from the /server/ directory.
 */
function import($file)
{
	// TODO: Does $base need to begin at $_SERVER["ROOT"]?
	//		 experiment in different environments to check.
	$base = 'server/';

	require_once "{$base}{$file}";
}


// Autoloader.
spl_autoload_register(function($class)
{
	$class = str_replace('\\', '/', $class);
	$path = "$class.php";

	import($path);

});



// Initialize the core Application.
new \app\Application();