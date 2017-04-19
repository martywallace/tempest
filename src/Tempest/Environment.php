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
			self::$_env = new DOtEnv($root);
			self::$_env->load();
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

}