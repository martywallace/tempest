<?php

namespace tempest\templating;

use \tempest\templating\Template;


class Manager
{

	private static $loaded = array();
	private static $hooks = array();


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


	public static function addHook($name, $callback)
	{
		self::$hooks[$name] = $callback;
	}


	public static function hasHook($name)
	{
		return array_key_exists($name, self::$hooks);
	}


	public static function applyHook($name, $value, $params = null)
	{
		$hook = self::$hooks[$name];
		return $hook($value, $params);
	}

}