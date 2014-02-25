<?php

namespace tempest\templating;

use \tempest\templating\Token;


class Template
{

	private static $cache = array();


	public static function load($file)
	{
		$file = preg_replace(PATTERN_SLASHES, DIRECTORY_SEPARATOR, $file);

		if(!array_key_exists($file, self::$cache))
		{
			self::$cache[$file] = self::prepare(file_get_contents(TEMPLATE_DIR . $file));
		}


		return self::$cache[$file];
	}


	public static function merge($base, $data)
	{
		$tokens = self::getTokens($base);

		if(is_array($data)) return self::mergeArray($base, $data, $tokens);
		return self::mergeObject($base, $data, $tokens);
	}


	public static function combine($chunks)
	{
		return implode($chunks);
	}


	private static function prepare($base)
	{
		return str_replace("~/", CLIENT_ROOT, $base);
	}


	private static function getTokens($base)
	{
		preg_match_all(PATTERN_TOKEN, $base, $matches);
		
		$tokens = array();
		for($i = 0; $i < count($matches[0]); $i++)
		{
			$tokens[] = new Token($matches[0][$i], $matches[1][$i], $matches[2][$i]);
		}


		return $tokens;
	}


	private static function mergeArray($base, $data, $tokens)
	{
		foreach($tokens as $token)
		{
			$value = $data;
			foreach($token->getParts() as $part)
			{
				if(array_key_exists($part, $value))
				{
					$value = $value[$part];
				}
				else
				{
					// Incomplete path, stop processing token.
					continue 2;
				}
			}

			$base = $token->replace($base, $value);
		}


		return $base;
	}


	private static function mergeObject($base, $data, $tokens)
	{
		// TODO.
		//

		return $base;
	}

}