<?php namespace Tempest;

use Dotenv\Dotenv;

/**
 * Provides access to environment variables.
 *
 * @author Marty Wallace
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
	 * Retrieve a raw value from the environment.
	 *
	 * @param string $var The environment variable name.
	 * @param string $fallback A fallback value to use if the variable did not exist.
	 *
	 * @return string
	 */
	public function get($var, $fallback = null) {
		return $this->string($var, $fallback);
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
		$value = getenv($var);

		return $value === false ? $fallback : $value;
	}

}