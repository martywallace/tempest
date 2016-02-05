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
	private static $_reflection;

	/**
	 * Get all the constants defined by this enum.
	 *
	 * @return array
	 */
	public static function getAll() {
		return self::_reflect()->getConstants();
	}

	/**
	 * @return ReflectionClass
	 */
	private static function _reflect() {
		if (empty(self::$_reflection)) {
			self::$_reflection = new ReflectionClass(static::class);
		}

		return self::$_reflection;
	}

}