<?php namespace Tempest\Utils;

/**
 * Utilities for working with arrays or performing actions related to arrays.
 *
 * @package Tempest\Utils
 * @author Marty Wallace
 */
class ArrayUtil {

	/**
	 * Force a value to be contained within an array. If the input value is already an array, nothing happens.
	 *
	 * @param mixed|mixed[] $value The input value.
	 *
	 * @return mixed[]
	 */
	public static function forceArray($value) {
		return !is_array($value) ? [$value] : $value;
	}

}