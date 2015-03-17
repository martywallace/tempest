<?php

/**
 * Prepend some data to the beginning of each key in an array.

 * @param array $array The target array.
 * @param string $prefix The data to prepend.
 */
function array_keys_prepend($array, $prefix)
{
	$new = array();
	foreach($array as $key => $value)
	{
		$new[$prefix . $key] = $value;
	}

	return $new;
}


/**
 * Returns a value if it is set, else return a fallback value.

 * @param mixed $value The value to get.
 * @param mixed $fallback The fallback value to use.
 */
function set_or($value, $fallback = null)
{
	return isset($value) ? $value : $fallback;
}