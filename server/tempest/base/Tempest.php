<?php

namespace tempest\base;

use \tempest\base\ErrorHandler;
use \tempest\routing\Router;
use \tempest\routing\Response;
use \tempest\templating\Template;
use \tempest\templating\BaseHookHandler;


class Tempest
{
	
	private $router;
	private $route;
	private $outputMime;
	private $outputData;
	private $errorHandler;


	public function __construct(ErrorHandler $errorHandler)
	{
		Template::setHookHandler(new BaseHookHandler());

		$this->errorHandler = $errorHandler;
		$this->router = new Router();

		$this->setup();
		$this->run();

		if($errorHandler->hasErrors())
		{
			// Display application errors.
			$this->setMime(MIME_HTML);
			$errorHandler->displayErrors();
		}
		else
		{
			// Print output.
			$this->setMime($this->outputMime);
			echo $this->outputData;
		}
	}


	private function run()
	{
		$this->route = $this->router->getRoute();

		if($this->route === null)
		{
			// No valid Route was found.
			trigger_error("Input route <code>" . REQUEST_URI . "</code> did not provide a response.");
		}
		else
		{
			// Matched a Route, construct Response and prepare output.
			$responseClass = $this->route->getResponseClass();
			$responseMethod = $this->route->getResponseMethod();
			$concreteResponseClass = '\\' . str_replace('.', '\\', RESPONSE_DIR . $responseClass);

			if(class_exists($concreteResponseClass))
			{
				$request = $this->router->getRequest();
				$response = new $concreteResponseClass($this, $request);

				if($response instanceof Response)
				{
					if(method_exists($response, $responseMethod))
					{
						$this->outputMime = $response->getMime();
						$this->outputData = $response->$responseMethod($request);
					}
					else
					{
						// Response did not have the relevant function.
						trigger_error("<code>$responseClass</code> does not define a function <code>$responseMethod</code>.");
					}
				}
				else
				{
					// Constructed object was not a Response.
					trigger_error("<code>$responseClass</code> is not an instance of <code>Response</code>.");
				}
			}
			else
			{
				// Route was valid, but the Response class was not found.
				trigger_error("Response <code>$responseClass</code> not found.");
			}
		}
	}


	protected function setup(){ /* Virtual */ }

	protected function setMime($value){ header("Content-type: $value"); }

	public function getRouter(){ return $this->router; }

}