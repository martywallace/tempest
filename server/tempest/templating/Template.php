<?php

namespace tempest\templating;

use \tempest\templating\Token;
use \tempest\templating\TokenPart;


class Template
{

	private static $cache = array();


	public static function load($file)
	{
		$file = preg_replace(PATTERN_SLASHES, DIRECTORY_SEPARATOR, $file);
		$path = TEMPLATE_DIR . $file;

		if(!array_key_exists($file, self::$cache))
		{
			if(file_exists($path))
			{
				self::$cache[$file] = self::prepare(file_get_contents($path));
			}
			else
			{
				trigger_error("Template <em><code>$file</code></em> does not exist.");
				return '';
			}
		}


		return self::$cache[$file];
	}


	public static function prepare($base)
	{
		return str_replace("~/", CLIENT_ROOT, $base);
	}


	public static function combine($chunks)
	{
		return implode($chunks);
	}


	public static function inject($base, $data, $context = null)
	{
		foreach(self::getTokens($base) as $token)
		{
			$value = $data;

			if($context === null && $token->isContextual()) continue;
			if($context !== null && !$token->isContextual()) continue;
			if($context !== null && $token->getContext() !== $context) continue;

			foreach($token->getParts() as $part)
			{
				$p = $part->getBase();

				if(is_array($value) && array_key_exists($p, $value)) $value = $value[$p];
				else if(is_object($value) && !$part->isFunction() && property_exists($value, $p)) $value = $value->$p;
				else if(is_object($value) && $part->isFunction() && method_exists($value, $part->getName())) $value = $value->{$part->getName()}();

				else continue 2;
			}

			$base = $token->replace($base, $value);
		}


		return $base;
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