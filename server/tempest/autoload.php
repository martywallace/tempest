<?php

// Class autoloader.
spl_autoload_register(function($class)
{
	foreach(array('tempest', 'vendor', 'app') as $path)
	{
		$path = APP_ROOT . "server/$path/" . str_replace('\\', '/', $class) . '.php';

		if(is_file($path))
		{
			require_once $path;
			return;
		}
	}

	trigger_error("Class <code>$path</code> not found.");

});