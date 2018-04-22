<?php namespace Tempest\Enums;

use ReflectionClass;

/**
 * A basic PHP enum implementation.
 *
 * @author Ascension Web Development
 */
abstract class Enum {

	/**
	 * Provide reflection information for this enum.
	 *
	 * @return ReflectionClass
	 */
	protected static function reflect() {
		return new ReflectionClass(static::class);
	}

	/**
	 * Return all defined constants within this enum.
	 *
	 * @return array
	 */
	public static function getValues() {
		return static::reflect()->getConstants();
	}

	/**
	 * Determine whether this enum contains the specified value.
	 *
	 * @param mixed $value The value to check for.
	 *
	 * @return bool
	 */
	public static function hasValue($value) {
		return in_array($value, static::getValues());
	}

}