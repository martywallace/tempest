<?php namespace Tempest;

use Tempest\Utils\Enum;


/**
 * Various default environment types for your application.
 *
 * @package Tempest
 * @author Marty Wallace
 */
class Environment extends Enum {

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
	public static function get($prop, $fallback = null) {
		$value = getenv($prop);

		return $value ? $value : $fallback;
	}

}