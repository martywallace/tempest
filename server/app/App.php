<?php

use Tempest\Tempest;
use Tempest\Config;
use Tempest\HTTP\Router;
use Tempest\HTTP\Request;
use Tempest\HTTP\Status;
use Tempest\Services\Templates;


class App extends Tempest
{
	
	protected function setup(Router $router)
	{
		// Load components.
		$this->addService('templates', new Templates($this));

		// Load application configuration data.
		Config::load("config.php");

		// Define application routes.
		$router->register(Config::data("routes"));
	}


	protected function errorOutput(Request $r, $code)
	{
		if($code === Status::NOT_FOUND)
		{
			// You are able to implement a custom 404 page here.
			return '404 - Not Found.';
		}


		// Use default error output if the code hasn't been handled.
		return parent::errorOutput($r, $code);
	}

}