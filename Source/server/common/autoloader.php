<?php

spl_autoload_register(function($class)
{
	foreach(array('vendor','app') as $path)
	{
		$path = DIR_SERVER . $path . DIRECTORY_SEPARATOR . str_replace('\\', DIRECTORY_SEPARATOR, $class) . '.php';

		if(is_file($path))
		{
			require_once $path;
			return;
		}
	}

	trigger_error("Class <code>$path</code> not found.");

});