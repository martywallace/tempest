<?php namespace Tempest\Utils;

use Exception;
use ReflectionClass;

/**
 * The enum class adds very basic enumeration functionality to classes who using constants to emulate real enums as seen
 * in languages like C#.
 *
 * @package Tempest\Utils
 * @author Marty Wallace
 */
abstract class Enum {

	/** @var string[] */
	private static $_reflections = array();

	/**
	 * Get the value of a constant using the name of the key associated with it.
	 *
	 * @param string $key The key to get the value for.
	 *
	 * @return mixed
	 *
	 * @throws Exception If the key does not exist.
	 */
	public static function getValue($key) {
		if (static::has($key)) {
			return static::getAll()[$key];
		} else {
			throw new Exception('Enum "' . static::class . '" does not contain "' . $key . '".');
		}
	}

	/**
	 * Determine whether this enum defined a key.
	 *
	 * @param string $key The name of the key.
	 *
	 * @return bool
	 */
	public static function has($key) {
		return array_key_exists($key, static::getAll());
	}

	/**
	 * Get all the constants defined by this enum.
	 *
	 * @return array
	 */
	public static function getAll() {
		return static::_reflect()->getConstants();
	}

	/**
	 * @return ReflectionClass
	 */
	protected static function _reflect() {
		if (!array_key_exists(static::class, self::$_reflections)) {
			self::$_reflections[static::class] = new ReflectionClass(static::class);
		}

		return self::$_reflections[static::class];
	}

}