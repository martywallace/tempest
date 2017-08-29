<?php namespace Tempest;

use Exception;

/**
 * General utilities.
 *
 * @author Marty Wallace
 */
class Utility {

	/**
	 * Dig into an instance and expose a descendant value using a dot (.) delimited query. The query can contain array
	 * keys, class properties and even class methods.
	 *
	 * @param mixed $instance The instance to dig for data.
	 * @param string $query A dot (.) delimited query representing the tree to follow when digging for a value.
	 * @param mixed $fallback A fallback value to provide if the descendant did not exist.
	 *
	 * @return mixed
	 */
	public static function dig($instance, $query, $fallback = null) {
		if (!empty($instance)) {
			$query = array_filter(explode('.', $query), function($value) {
				// Remove any sneaky empty values.
				return strlen(trim($value)) !== 0;
			});

			if (!empty($query)) {
				$target = $instance;

				foreach ($query as $prop) {
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
	 * Convert a string into kebab format e.g. "The quick brown fox" becomes "the-quick-brown-fox".
	 *
	 * @param string $value The input value.
	 * @param bool $upper Whether or no to uppercase the first letter of each segment e.g. "The-Quick-Brown-Fox".
	 *
	 * @return string
	 */
	public static function kebab($value, $upper = false) {
		$base = preg_replace('/[^A-Za-z\s\-]+/', '', $value);
		$base = preg_replace('/[\s\-]+/', '-', $base);
		$base = strtolower($base);

		if ($upper) {
			$base = implode('-', array_map(function ($part) {
				return ucfirst($part);
			}, explode('-', $base)));
		}

		return trim(trim($base, '-'));
	}

}