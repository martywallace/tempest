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


	public static function combine($chunks)
	{
		return implode($chunks);
	}


	public static function injectA($base, $data)
	{
		foreach(self::getTokens($base) as $token)
		{
			$value = $data;
			foreach($token->getParts() as $part)
			{
				if($token->isContextual()) continue 2;

				if(is_array($value) && array_key_exists($part, $value)) $value = $value[$part];
				else if(is_object($value) && property_exists($value, $part)) $value = $value->$part;
				else continue 2;	
			}

			$base = $token->replace($base, $value);
		}


		return $base;
	}


	public static function injectB($base, $context, $data)
	{
		foreach(self::getTokens($base) as $token)
		{
			if($token->getContext() === $context)
			{
				// Token matches context.
				$value = $data;
				foreach($token->getParts() as $part)
				{
					if(is_object($value) && property_exists($value, $part)) $value = $value->$part;
					else if(is_array($value) && array_key_exists($part, $value)) $value = $value[$part];
					else continue 2;
				}

				$base = $token->replace($base, $value);
			}
		}

		return $base;
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
			$tokens[] = new Token(
				$matches[0][$i],
				$matches[1][$i],
				$matches[2][$i]
			);
		}


		return $tokens;
	}

}