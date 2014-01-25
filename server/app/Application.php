<?php

namespace app;

use \tempest\base\Tempest;


class Application extends Tempest
{

	protected function setup()
	{
		$routes = array(
			"home" => "app.handlers.Page",
			"about" => "app.handlers.Page",
			"page/2" => "app.handlers.Page",
			"blog/post/[id]" => "app.handlers.Page",
			"some///page?hello=1" => "app.handlers.Page",
			"anotherthing#test" => "app.handlers.Page",
			"test/[hello]/[there]" => "app.handlers.Page"
		);

		$this->getRouter()->map($routes);
	}
	
}