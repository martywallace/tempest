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


	protected function defineServices()
	{
		return array(
			// Add services here. Services are accessible in Twig via {{ tempest.<serviceName> }}.
			// ...
		);
	}

}