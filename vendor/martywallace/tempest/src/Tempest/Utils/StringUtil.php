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
		$base = preg_replace('/[^\w\s]+/', '', $value);
		$base = preg_replace('/\s+/', '-', $base);

		return trim(strtolower($base));
	}

}