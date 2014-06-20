<?php namespace Tempest\Utils;

class FileHelper
{
	
	private static $loaded = [];


	public static function getContents($file)
	{
		if(!array_key_exists($file, self::$loaded))
		{
			if(is_file($file))
			{
				// File load successful.
				self::$loaded[$file] = file_get_contents($file);
			}
			else
			{
				trigger_error("File <code>$file</code> could not be loaded.");
				return null;
			}
		}

		return self::$loaded[$file];
	}

}