<?php namespace Tempest\Base;

use Tempest\Utils\FileHelper;


class Config
{
	
	public function __construct()
	{
		$json = json_decode(FileHelper::getContents("server/config.json"));

		foreach($json as $prop => $value)
		{
			// Move config properties to config class.
			$this->$prop = $value;
		}
	}

}