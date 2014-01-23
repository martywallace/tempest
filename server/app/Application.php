<?php

namespace app;

use \tempest\base\Tempest;


class Application extends Tempest
{

	protected function setup()
	{
		$routes = array(
			"home" => "app.handlers.Home",
			"about" => "app.handlers.Home"
		);

		$this->getRouter()->map($routes);
	}
	
}