<?php

function path_normalize($path, $separator = DIRECTORY_SEPARATOR, $head = false, $tail = false)
{
	if(strlen($path) === 0 || $path === '/' || $path === '\\' || $path === $separator) return $separator;

	$base = preg_replace(RGX_PATH_DELIMITER, $separator, $path);
	$base = trim($base, $separator);

	$base = $head ? $separator . $base : $base;
	$base = $tail ? $base . $separator : $base;

	return $base;
}


function path_split($path)
{
	$base = trim($path, '/\\ ');
	return preg_split(RGX_PATH_DELIMITER, $base);
}


function dtrim($value, $left, $right)
{
	$base = ltrim($value, $left);
	return rtrim($base, $right);
}


function array_keys_prepend($array, $prefix)
{
	$new = [];
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


function pre_print_r($data)
{
	echo '<pre>';
	print_r($data);
	echo '</pre>';
}