<?php namespace Tempest\Utils;

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