<?php namespace Tempest\Utils;

class FileHelper
{
	
	private static $loaded = array();


	public static function getContents($file)
	{
		if(!array_key_exists($file, self::$loaded))
		{
			$path = path_normalize($file, SEP, false, false);

			if(is_file($path))
			{
				// File load successful.
				self::$loaded[$file] = file_get_contents($file);
			}
			else
			{
				trigger_error("File <code>$path</code> could not be loaded.");
				return null;
			}
		}

		return self::$loaded[$file];
	}

}