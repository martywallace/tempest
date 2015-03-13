<?php

use Tempest\Tempest;
use Tempest\HTTP\Router;


class App extends Tempest
{
	
	protected function setup(Router $router)
	{
		// Define application routes.
		$router->register($this->config("routes"));
	}


	protected function defineServices()
	{
		return array(
			// Add services here. Services are accessible in Twig via {{ tempest.<serviceName> }}.
			// ...
		);
	}

}