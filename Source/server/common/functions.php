<?php

function path_normalize($path, $separator = DIRECTORY_SEPARATOR, $trailingSlash = true)
{
	if(strlen($path) === 0 || $path === '/' || $path === '\\' || $path === $separator) return $separator;

	$base = preg_replace('/[\/\\\\]+/', $separator, $path);
	$base = rtrim($base, $separator);

	return $trailingSlash ? $base . $separator : $base;
}


function path_split($path)
{
	$base = trim($path, '/\\ ');
	return preg_split('/[\/\\\\]+/', $base);
}


function str_comma_join(Array $input)
{
	return implode(',', $input);
}


function str_needle_remove($needle, $haystack)
{
	return str_replace($needle, '', $haystack);
}