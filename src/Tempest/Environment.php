<?php namespace Tempest;

use Tempest\Utils\Enum;


/**
 * Various default environment types for your application.
 *
 * @package Tempest
 * @author Marty Wallace
 */
class Environment extends Enum {

	/** @var string */
	private static $_envVarName = 'TEMPEST_ENV';

	const ALL = '*';
	const DEV = 'dev';
	const STAGE = 'stage';
	const PROD = 'prod';

	/**
	 * Sets the name of the environment variable used to determine the application environment.
	 *
	 * @param string $var The new
	 */
	public static function setEnvironmentVarName($var) {
		self::$_envVarName = $var;
	}

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
	 * Get the current environment.
	 *
	 * @see Environment::DEV
	 * @see Environment::STAGE
	 * @see Environment::PROD
	 *
	 * @return string
	 */
	public static function current() {
		return self::prop(self::$_envVarName, self::DEV);
	}

}