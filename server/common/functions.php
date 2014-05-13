<?php

function path_normalize($path, $separator = DIRECTORY_SEPARATOR, $leadingSlash = true, $trailingSlash = true)
{
	if(strlen($path) === 0 || $path === '/' || $path === '\\' || $path === $separator) return $separator;

	$base = preg_replace(RGX_PATH_DELIMITER, $separator, $path);
	$base = trim($base, $separator);

	$base = $leadingSlash ? $separator . $base : $base;
	$base = $trailingSlash ? $base . $separator : $base;

	return $base;
}


function path_split($path)
{
	$base = trim($path, '/\\ ');
	return preg_split(RGX_PATH_DELIMITER, $base);
}


function str_comma_join(Array $input)
{
	return implode(',', $input);
}


function str_needle_remove($needle, $haystack)
{
	return str_replace($needle, '', $haystack);
}