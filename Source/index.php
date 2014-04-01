<?php

// Application constants.
// =================================================================================================
define("ROOT", __DIR__);
define("PUBL", normalize_path(dirname($_SERVER["PHP_SELF"]), '/'));



// Global methods.
// =================================================================================================
function normalize_path($path, $separator = DIRECTORY_SEPARATOR, $trailingSlash = true)
{
	if(strlen($path) === 0 || $path === '/' || $path === '\\' || $path === $separator) return $separator;

	$base = preg_replace('/[\/\\\\]+/', $separator, $path);
	$base = rtrim($base, $separator);

	return $trailingSlash ? $base . $separator : $base;
}



// Autoloader definition.
// Searches in <code>server/vendor</code> and <code>server/app</code> for classes.
// =================================================================================================
spl_autoload_register(function($class)
{
	foreach(array('vendor','app') as $path)
	{
		$path = ROOT . DIRECTORY_SEPARATOR . 'server' . DIRECTORY_SEPARATOR . $path . DIRECTORY_SEPARATOR . str_replace('\\', DIRECTORY_SEPARATOR, $class) . '.php';
		if(is_file($path)) require_once $path;
	}

});


// Initialize the Application.
// =================================================================================================
new App\Application;