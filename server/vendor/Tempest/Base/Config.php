<?php namespace Tempest\Base;


/**
 * Manages application configuration, defined in <code>/config.php</code>.
 * @author Marty Wallace.
 */
class Config
{

	private static $data = array();


	/**
	 * Loads a configuration file.
	 * @param string $file The target PHP file to load data from. The file must 'return' an array of the config data.
	 */
	public static function load($file = 'config.php')
	{
		self::$data = require_once(APP_ROOT . $file);

		// General configuration.
		date_default_timezone_set(self::data("timezone", "Australia/Sydney"));
	}


	/**
	 * Returns configuration data.
	 * @param $field string The configuration data to get.
	 * @param $default mixed A default value to use, if the data was not found.
	 * @return mixed The result data, or the default value if it was not found.
	 */
	public static function data($field = null, $default = null)
	{
		if($field === null) return self::$data;

		$path = preg_split('/\.+/', $field);
		if(!array_key_exists($path[0], self::$data)) return $default;

		$target = self::$data;
		foreach($path as $p)
		{
			if(array_key_exists($p, $target)) $target = $target[$p];
			else return $default;
		}

		return $target;
	}

}