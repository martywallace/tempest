<?php

use Tempest\Tempest;
use Tempest\HTTP\Router;
use Tempest\HTTP\Request;
use Tempest\HTTP\Status;


class App extends Tempest
{
	
	protected function setup(Router $router)
	{
		// Define application routes.
		$router->register($this->config("routes"));

		tempest()->db->connect('db');
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