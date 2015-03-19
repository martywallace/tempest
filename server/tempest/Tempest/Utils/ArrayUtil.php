<?php namespace Tempest\Utils;

/**
 * Utilities for working with Arrays.
 *
 * @author Marty Wallace.
 */
class ArrayUtil
{

	/**
	 * Pluck the value of a specified property from each item in an input array and return the
	 * result array.
	 *
	 * @param array $input The input array.
	 * @param string $property The property to pluck from each item in the input array.
	 *
	 * @return array
	 */
	public static function pluck($input, $property)
	{
		$output = array();
		foreach ($input as $object)
		{
			if (is_array($object) && array_key_exists($property, $object)) $output[] = $object[$property];
			if (is_object($object) && property_exists($object, $property)) $output[] = $object->{$property};
		}

		return $output;
	}

}