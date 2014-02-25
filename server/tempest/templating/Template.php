<?php

namespace tempest\templating;

use \tempest\templating\Token;
use \tempest\templating\Behaviour;


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


	public static function merge($base, $data, array $behaviours = null)
	{
		$tokens = self::getTokens($base);

		if(is_array($data)) return self::mergeArray($base, $data, $tokens, $behaviours);
		return self::mergeObject($base, $data, $tokens, $behaviours);
	}


	private static function prepare($base)
	{
		return str_replace("~/", CLIENT_ROOT, $base);
	}


	private static function getTokens($base)
	{
		preg_match_all(PATTERN_TPL_TOKEN, $base, $matches);
		
		$tokens = array();
		for($i = 0; $i < count($matches[0]); $i++)
		{
			$tokens[] = new Token(
				$matches[0][$i],
				$matches[1][$i],
				$matches[2][$i]
			);
		}


		return $tokens;
	}


	private static function mergeArray($base, $data, $tokens, array $behaviours = null)
	{
		foreach($tokens as $token)
		{
			if(array_key_exists($token->getName(), $data))
			{
				$base = $token->replace($base, $data[$token->getName()]);
			}
		}


		return $base;
	}


	private static function mergeObject($base, $data, $tokens, array $behaviours = null)
	{
		return $base;
	}

}