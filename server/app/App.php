<?php

use Tempest\Tempest;
use Tempest\HTTP\Router;


class App extends Tempest
{
	
	protected function setup()
	{
		// Application setup.
		// ...
	}


	protected function defineRoutes(Router $router)
	{
		// Use the routes defined in the app configuration.
		return tempest()->config('routes', array());
	}


	protected function defineServices()
	{
		return array(
			// Add services here. Services are accessible in Twig via {{ tempest.<serviceName> }}.
			// ...
		);
	}

}