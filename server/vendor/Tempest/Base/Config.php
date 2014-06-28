<?php namespace Tempest\Base;


/**
 * Manages application configuration, defined in <code>/config.php</code>.
 * @author Marty Wallace.
 */
class Config
{

	private $required = ['routes'];
	private $data;

	
	/**
	 * Constructor.
	 */
	public function __construct()
	{
		$this->data = require_once(APP_ROOT . 'config.php');

		foreach($this->required as $r)
		{
			if(!array_key_exists($r, $this->data)) trigger_error("Missing configuration requirement <code>$r</code>.");
		}
	}


	/**
	 * Returns the configuration data.
	 */
	public function getData(){ return $this->data; }

}