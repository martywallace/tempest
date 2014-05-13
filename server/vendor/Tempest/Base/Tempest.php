<?php namespace Tempest\Base;

use Tempest\Routing\Router;


class Tempest
{

	protected $router;
	protected $route;
	protected $mime;
	protected $output;

	
	public static function init()
	{
		new static();
	}


	public function __construct()
	{
		$this->router = new Router();
		$this->setup();
		$this->route = $this->router->getMatch();

		if($this->route !== null)
		{
			$class = preg_replace('/\.+/', '\\', 'App\\Responses\\' . $this->route->getHandlerClass());
			$method = $this->route->getHandlerMethod();
			$response = new $class();

			if(method_exists($response, $method))
			{
				$response->setup();

				$this->output = $response->$method($this->router->getRequest());
				$this->mime = $response->getMime();
				$this->finalize();
			}
			else
			{
				// Response class was constructed successfully, but the target method was not defined.
				trigger_error("Response class does not have the method <code>$method()</code>.");
			}
		}
		else
		{
			// No matching routes.
			trigger_error("Input route <code>{$this->router->getRequest()}</code> not handled.");
		}
	}


	private function finalize()
	{
		header("Content-type: {$this->mime}");
		echo $this->output;
		exit;
	}


	protected function setup(){ /**/ }

}