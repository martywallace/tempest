<?php

namespace tempest\utils;


class JSONFile
{

	public $data;

	private $path;


	public function __construct($path, $assoc = false)
	{
		if(is_file($path))
		{
			$this->path = $path;
			$this->data = json_decode(file_get_contents($path), $assoc);

			if(json_last_error() !== JSON_ERROR_NONE)
			{
				trigger_error("Invalid JSON at <code>$path</code>.");
			}
		}
		else
		{
			trigger_error("JSON file <code>$path</code> does not exist.");
		}
	}


	public function commit()
	{
		if(!file_put_contents($this->path, json_encode($this->data)))
		{
			trigger_error("Could not commit JSON file <code>$this->path</code>.");
		}
	}


	public function getPath(){ return $this->path; }
	public function getDirectory(){ return dirname($this->path); }
	public function getFilename(){ return basename($this->path); }

}