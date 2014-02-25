<?php

namespace app;

use \tempest\base\Tempest;


class Application extends Tempest
{

	protected function setup()
	{
		$this->getRouter()->map(array(
			"home" => "app.responses.DemoPage"
		));
	}
	
}