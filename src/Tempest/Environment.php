<?php namespace Tempest;

use Tempest\Utils\Enum;


/**
 * Various default environment types for your application.
 *
 * @package Tempest
 * @author Marty Wallace
 */
class Environment extends Enum {

	const ENV_VAR_NAME = 'TempestEnv';

	const ALL = '*';
	const DEV = 'dev';
	const STAGE = 'stage';
	const PROD = 'prod';

	/**
	 * Get an environment variable.
	 *
	 * @param string $prop The name of the environment variable to get.
	 * @param mixed $fallback The fallback value to use if the environment variable does not exist.
	 *
	 * @return string
	 */
	public static function prop($prop, $fallback = null) {
		$value = getenv($prop);

		return $value ? $value : $fallback;
	}

	/**
	 * Get the current environment mode (defined by {@link Environment::ENV_VAR_NAME} on the server).
	 *
	 * @see Environment::DEV
	 * @see Environment::STAGE
	 * @see Environment::PROD
	 *
	 * @return string
	 */
	public static function current() {
		return self::prop(self::ENV_VAR_NAME, self::DEV);
	}

}