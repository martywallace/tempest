<?php namespace Tempest\Utils;

/**
 * Utilities for working with Strings.
 * @author Marty Wallace.
 */
class StringUtil
{

	/**
	 * Replace non-word characters with hyphens and return the lowercased result.
	 * @param string $value The input string.
	 */
	public static function hyphenate($value)
	{
		return strtolower(preg_replace('/[^\w]+/', '-', $value));
	}


	/**
	 * Returns the first item from the result of <code>explode()</code> on the input value.
	 * @param string $value The input value.
	 * @param string $separator The separator to use for <code>explode()</code>.
	 */
	public static function firstPart($value, $separator = ' ')
	{
		$haystack = explode($separator, $value);
		return $haystack[0];
	}

}