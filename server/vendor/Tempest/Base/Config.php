<?php namespace Tempest\Base;


/**
 * Manages application configuration, defined in <code>/config.php</code>.
 * @author Marty Wallace.
 */
class Config
{

	private $required = ['title', 'timezone', 'routes'];
	private $data;

	
	/**
	 * Constructor.
	 */
	public function __construct()
	{
		$this->data = require_once(APP_ROOT . 'config.php');

		foreach($this->required as $r)
		{
			if(!array_key_exists($r, $this->data))
				trigger_error("Missing configuration requirement: <code>$r</code>.");
		}


		// General configuration.
		date_default_timezone_set($this->data["timezone"]);
	}


	/**
	 * Returns configuration data.
	 * @param $field Optional inner field to capture data from. Dot delimited values can be supplied
	 * when searching for nested values.
	 */
	public function data($field = null)
	{
		if($field === null) return $this->data;

		$path = preg_split('/\.+/', $field);
		$valid = [];
		$target = $this->data;

		foreach($path as $p)
		{
			if(array_key_exists($p, $target))
			{
				$target = $target[$p];
				$valid[] = $p;
			}
			else
			{
				// Field was invalid.
				trigger_error("Configuration property <code>$p</code> does not exist at <code>" . implode('.', $valid) . "</code>.");
			}
		}

		return $target;
	}

}