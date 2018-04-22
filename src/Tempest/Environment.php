<?php namespace Tempest;

use Dotenv\Dotenv;

/**
 * Provides access to environment variables.
 *
 * @author Ascension Web Development
 */
class Environment {

	public function __construct() {
		if (file_exists(App::get()->root . DIRECTORY_SEPARATOR . '.env')) {
			// Load variables from a .env file in the application root.
			$env = new Dotenv(App::get()->root);
			$env->load();
		}
	}

	/**
	 * Retrieve all set environment variables.
	 *
	 * @return array
	 */
	public function all() {
		return getenv();
	}

	/**
	 * Retrieve a raw value from the environment.
	 *
	 * @param string $var The environment variable name.
	 * @param string $fallback A fallback value to use if the variable did not exist.
	 *
	 * @return string
	 */
	public function get($var, $fallback = null) {
		$value = getenv($var);
		return $value === false ? $fallback : $value;
	}

	/**
	 * Alias of {@link get()} - retrieve a string value from the environment.
	 *
	 * @param string $var The environment variable name.
	 * @param string $fallback A fallback value to use if the variable did not exist.
	 *
	 * @return string
	 */
	public function string($var, $fallback = null) {
		return $this->get($var, $fallback);
	}

	/**
	 * Retrieve a boolean value for the environment. Values "true", "yes" and "1" are treated as true and everything
	 * else as false.
	 *
	 * @param string $var The environment variable name.
	 *
	 * @return bool
	 */
	public function bool($var) {
		$value = strtolower($this->get($var));
		return in_array($value, ['true', 'yes', '1']);
	}

	/**
	 * Retrieve an int value from the environment.
	 *
	 * @param string $var The environment variable name.
	 * @param int $fallback A fallback value to use if the variable did not exist.
	 *
	 * @return int
	 */
	public function int($var, $fallback = 0) {
		return intval($this->get($var, $fallback));
	}

	/**
	 * Retrieve a float value from the environment.
	 *
	 * @param string $var The environment variable name.
	 * @param float $fallback A fallback value to use if the variable did not exist.
	 *
	 * @return float
	 */
	public function float($var, $fallback = 0.0) {
		return floatval($this->get($var, $fallback));
	}

}