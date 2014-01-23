<?php

namespace tempest\base;

use \tempest\routing\Router;


class Tempest
{
	
	private $router;
	private $route;


	public function __construct()
	{
		$this->router = new Router();
		$this->setup();
		$this->run();
	}


	private function run()
	{
		$this->route = $this->router->getRoute();

		if($this->route === null)
		{
			// No valid Route was found.
			header("HTTP/1.0 404 Not Found");
			die();
		}
		else
		{
			// Matched a Route, construct Handler and prepare output.
			$def = $this->route->getHandler();
			$def = '\\' . str_replace('.', '\\', $def);

			if(class_exists($def))
			{
				$handler = new $def($this);

				header("Content-type: {$handler->getMime()}");
				echo $handler->getOutput();
			}
			else
			{
				// Route was valid, but the Handler was not found.
				echo "Handler {$this->route->getHandler()} not found.";
			}
		}
	}


	protected function setup(){ /* Virtual */ }


	protected function getRouter(){ return $this->router; }
	protected function getRoute(){ return $this->route; }

}