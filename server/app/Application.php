<?php

namespace app;

use \tempest\base\Tempest;


class Application extends Tempest
{

	protected function setup()
	{
		$routes = array(
			"home" => "app.handlers.Page"
		);

		$this->getRouter()->map($routes);
	}
	
}