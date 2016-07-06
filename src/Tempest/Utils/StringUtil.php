<?php namespace Tempest\Utils;

/**
 * Utilities dealing with strings.
 *
 * @package Tempest\Utils
 * @author Marty Wallace.
 */
class StringUtil {

	/**
	 * Slugify some text, removing any non-word characters and replacing whitespace with hyphens e.g. "My name is John"
	 * becomes "my-name-is-john".
	 *
	 * @param string $value The input text.
	 *
	 * @return string
	 */
	public static function slugify($value) {
		$base = preg_replace('/[^\w\s\-]+/', '', $value);
		$base = preg_replace('/[\s\-]+/', '-', $base);

		return trim(strtolower($base));
	}

	/**
	 * Converts a snake_case_string into a camelCaseString.
	 *
	 * @param string $value The input text.
	 *
	 * @return string
	 */
	public static function snakeCaseToCamelCase($value) {
		return preg_replace_callback('/(_+\w)/', function($matches) {
			return strtoupper(substr($matches[0], 1));
		}, $value);
	}

	/**
	 * Convers a camelCaseString into a snake_case_string.
	 *
	 * @param string $value The input text.
	 *
	 * @return string
	 */
	public static function camelCaseToSnakeCase($value) {
		return preg_replace_callback('/([A-Z]+)/', function($matches) {
			return '_' . strtolower($matches[0]);
		}, $value);
	}

}