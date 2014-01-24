<?php

namespace tempest\base;

use \tempest\routing\Router;
use \tempest\routing\Response;


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
			// Matched a Route, construct Response and prepare output.
			$def = $this->route->getResponse();
			$def = '\\' . str_replace('.', '\\', $def);

			if(class_exists($def))
			{
				$response = new $def($this);

				if($response instanceof Response)
				{
					header("Content-type: {$response->getMime()}");
					echo $response->getOutput();
				}
				else
				{
					// Constructed object was not a Response.
					echo "{$this->route->getResponse()} is not a Response object.";
				}
			}
			else
			{
				// Route was valid, but the Response class was not found.
				echo "Response {$this->route->getResponse()} not found.";
			}
		}
	}


	protected function setup(){ /* Virtual */ }


	protected function getRouter(){ return $this->router; }
	protected function getRoute(){ return $this->route; }

}