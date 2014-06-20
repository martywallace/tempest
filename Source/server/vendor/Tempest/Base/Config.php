<?php namespace Tempest\Base;

use Tempest\Utils\FileHelper;


class Config
{

	private $required = ['routes'];

	
	public function __construct()
	{
		$json = json_decode(FileHelper::getContents("server/config.json"));

		foreach($json as $prop => $value)
		{
			// Move config properties to config class.
			$this->$prop = $value;
		}

		foreach($this->required as $r)
		{
			if(!property_exists($this, $r)) trigger_error("Missing configuration requirement <code>$r</code>.");
		}
	}

}