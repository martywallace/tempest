<?php namespace Tempest\Base;

use Tempest\Routing\Router;


class Tempest
{

	protected $router;

	
	public static function init()
	{
		new static();
	}


	public function __construct()
	{
		$this->router = new Router();
		$this->setup();

		$this->route = $this->router->getMatch();

		if($this->route !== null)
		{
			echo 'success';
		}
		else
		{
			trigger_error("No input route.");
		}

		$this->finalize();
	}


	protected function setup(){ /**/ }
	protected function finalize(){ /**/ }

}