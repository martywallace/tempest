<?php

use Tempest\Base\Tempest;
use Tempest\Base\Config;
use Tempest\HTTP\Router;
use Tempest\HTTP\Request;
use Tempest\HTTP\Status;


class App extends Tempest
{
	
	protected function setup(Router $router)
	{
		// Load application configuration data.
		Config::load("config.php");

		// Define application routes.
		$router->register(Config::data("routes"));
	}


	protected function getErrorOutput(Request $r, $code)
	{
		if($code === Status::NOT_FOUND)
		{
			// You are able to implement a custom 404 page here.
			return '404 - Not Found.';
		}

		return parent::getErrorOutput($r, $code);
	}

}