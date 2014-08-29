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
		if(array_key_exists("timezone", self::$data)) date_default_timezone_set(self::$data["timezone"]);
	}


	/**
	 * Returns configuration data.
	 * @param $field string The configuration data to get.
	 * @return mixed The result data.
	 */
	public static function data($field = null)
	{
		if($field === null) return self::$data;

		$path = preg_split('/\.+/', $field);
		$valid = array();
		$target = self::$data;

		foreach($path as $p)
		{
			if(array_key_exists($p, $target))
			{
				$target = $target[$p];
				$valid[] = $p;
			}
			else
			{
				// Field was invalid.
				trigger_error("Configuration property <code>$p</code> does not exist at <code>" . implode('.', $valid) . "</code>.");
			}
		}

		return $target;
	}

}