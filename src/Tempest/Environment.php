<?php namespace Tempest;

use Exception;
use Dotenv\Dotenv;


/**
 * A thin layer providing enchanced access to environment variables.
 *
 * @package Tempest
 * @author Marty Wallace
 */
class Environment {

	/** @var DotEnv */
	private static $_env = null;

	/**
	 * Load the environment.
	 *
	 * @param string $root The application root, where the .env file will be loaded.
	 *
	 * @throws Exception If the environment has already been loaded.
	 */
	public static function load($root) {
		if (empty(self::$_env)) {
			if (is_file($root . '.env')) {
				self::$_env = new DotEnv($root);
				self::$_env->load();

				self::$_env->required('dev')->allowedValues(['true', 'false']);
			} else {
				// No .env variables declared.
				// ...
			}
		} else {
			throw new Exception('Attempting to reload the environment.');
		}
	}

	/**
	 * Get an environment variable.
	 *
	 * @param string $prop The name of the environment variable to get.
	 * @param mixed $fallback The fallback value to use if the environment variable does not exist.
	 *
	 * @return string
	 */
	public static function get($prop, $fallback = null) {
		$value = getenv($prop);

		return $value ? $value : $fallback;
	}

	/**
	 * Get an environment variable and cast it to a boolean. If the environment variable is the string null, false or 0
	 * it will be considered false.
	 *
	 * @param string $prop The name of the environment variable to get.
	 * @param bool $fallback THe fallback value to use of the environment variable does not exist.
	 *
	 * @return bool
	 */
	public static function getBool($prop, $fallback = false) {
		$value = strtolower(self::get($prop, $fallback));

		if ($value === 'false' || $value === 'null' || $value === '0') {
			// The value should be interpreted as false.
			$value = false;
		}

		return !!$value;
	}

}