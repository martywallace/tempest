<?php namespace Tempest\Utils;

use Exception;

/**
 * Utilities related to general objects and arrays.
 *
 * @package Tempest\Utils
 * @author Marty Wallace
 */
class ObjectUtil {

	/**
	 * Attempt to get the value of a sub-property (a property of another property of the target instance).
	 *
	 * @param mixed $instance The target instance. It can be either an object or an array.
	 * @param string $path A dot delimited path to the sub-property. Sub-properties can include property names, array
	 * indexes and methods to be called.
	 * @param mixed $fallback A fallback value to use if the property did not exist.
	 *
	 * @return mixed
	 */
	public static function getDeepValue($instance, $path, $fallback = null) {
		if (!empty($instance)) {
			$path = array_filter(explode('.', $path), function($value) {
				// Remove any sneaky empty values.
				return strlen(trim($value)) !== 0;
			});

			if (!empty($path)) {
				$target = $instance;

				foreach ($path as $prop) {
					if (is_array($target) && array_key_exists($prop, $target)) {
						if (is_callable($target[$prop])) $target = $target[$prop]();
						else $target = $target[$prop];
					}

					else if (is_object($target) && property_exists($target, $prop)) $target = $target->{$prop};
					else if (is_object($target) && method_exists($target, $prop)) $target = $target->{$prop}();

					else return $fallback;
				}

				return $target;
			}
		}

		return $fallback;
	}

	/**
	 * Returns an array of plucked values. The values are plucked from an array of nested arrays or objects using their
	 * keys or properties.
	 *
	 * @param array $values An array of arrays or objects to pluck properties from.
	 * @param string $property The property or key to pluck from each item.
	 *
	 * @return array
	 *
	 * @throws Exception If the provided value is not an array.
	 */
	public function pluck(array $values, $property) {
		if (is_array($values)) {
			$result = array();

			foreach ($values as $value) {
				if (is_array($value) && array_key_exists($property, $value)) $result[] = $value[$property];
				else if (is_object($value) && property_exists($value, $property)) $result[] = $value->{$property};
			}

			return $result;
		} else {
			throw new Exception('pluck() expects an array.');
		}
	}

}