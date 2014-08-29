<?php

use Tempest\Base\Tempest;
use Tempest\Base\Config;
use Tempest\HTTP\Router;


class App extends Tempest
{
	
	protected function setup(Router $router)
	{
		// Load application configuration data.
		Config::load("config.php");

		// Define application routes.
		$router->register(Config::data("routes"));
	}

}