<?php


function path_split($path)
{
	$base = trim($path, '/\\ ');
	return preg_split('/\\\+/', $base);
}


function array_keys_prepend($array, $prefix)
{
	$new = array();
	foreach($array as $key => $value)
	{
		$new[$prefix . $key] = $value;
	}

	return $new;
}


function set_or($value, $fallback = null)
{
	return isset($value) ? $value : $fallback;
}