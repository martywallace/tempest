<?php

// Class autoloader.
spl_autoload_register(function($class)
{
	foreach(array('vendor', 'app') as $path)
	{
		$path = DIR . SEP . 'server' . SEP . $path . SEP . str_replace('\\', SEP, $class) . '.php';

		if(is_file($path))
		{
			require_once $path;
			return;
		}
	}

	trigger_error("Class <code>$path</code> not found.");

});