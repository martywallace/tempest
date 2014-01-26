<?php

namespace tempest\templating;

use \tempest\templating\Template;


class Manager
{

	private static $loaded = array();


	public static function load($file)
	{
		if(array_key_exists($file, self::$loaded))
		{
			// This template has already been loaded, use existing result.
			return self::$loaded[$file];
		}

		$path = APP_ROOT . DIRECTORY_SEPARATOR . 'view' . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . $file;

		if(file_exists($path))
		{
			self::$loaded[$file] = file_get_contents($path);
			return self::$loaded[$file];
		}

		return "Template <code>$file</code> could not be loaded.";
	}

}