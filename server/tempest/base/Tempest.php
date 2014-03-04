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
			echo "The page was not found.";
		}
		else
		{
			// Matched a Route, construct Response and prepare output.
			$rclass = $this->route->getResponseClass();
			$rclass = '\\' . str_replace('.', '\\', RESPONSE_DIR . $rclass);

			$rmethod = $this->route->getResponseMethod();

			if(class_exists($rclass))
			{
				$response = new $rclass($this);
				if($response instanceof Response)
				{
					if(method_exists($response, $rmethod))
					{
						$this->setMime($response->getMime());
						echo $response->$rmethod($this->router->getRequest());
					}
					else
					{
						// Response did not have the relevant function.
						trigger_error("Response does not implement <code>$rmethod</code>.");
					}
				}
				else
				{
					// Constructed object was not a Response.
					trigger_error("<code>{$this->route->getResponse()}</code> must be an instance of <code>Response</code>.");
				}
			}
			else
			{
				// Route was valid, but the Response class was not found.
				trigger_error("Response <code>{$this->route->getResponse()}</code> not found.");
			}
		}
	}


	protected function setup(){ /* Virtual */ }


	public function getRouter(){ return $this->router; }
	public function getRoute(){ return $this->route; }
	public function setMime($value){ header("Content-type: $value"); }

}