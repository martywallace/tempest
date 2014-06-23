<?php namespace Tempest\Base;

use Tempest\Routing\Router;
use Tempest\Base\Error;
use Tempest\Templating\Template;


class Tempest
{

	private $router;
	private $route;
	private $config;
	private $mime;
	private $output = '';
	private $errors = [];


	public function start()
	{
		$this->router = new Router();
		$this->config = new Config();
		$this->router->register($this->config->routes);
		$this->setup();
		$this->route = $this->router->getMatch();

		if($this->route !== null)
		{
			$class = preg_replace('/\.+/', '\\', 'Responses\\' . $this->route->getHandlerClass());
			$method = $this->route->getHandlerMethod();
			$response = new $class($this);

			if(method_exists($response, $method))
			{
				$req = $this->router->getRequest();

				$response->setup($req);

				$this->output = $response->$method($req);
				$this->mime = $response->getMime();
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

		if(count($this->errors) > 0)
		{
			// Errors found, use error output.
			$this->mime = 'text/html';
			$this->output = Template::load('/templates/tempest/errors.html')->bind([
				"errors" => Template::batch(Template::load('/templates/tempest/error.html'), $this->errors)
			]);
		}
		
		$this->finalize();
	}


	public function error($number, $string, $file, $line, $context)
	{
		$this->errors[] = new Error($number, $string, $file, $line, $context);
	}


	private function finalize()
	{
		header("Content-type: {$this->mime}");

		if(is_a($this->output, 'Tempest\Templating\Template'))
		{
			$request = $this->router->getRequest();
			$reqData = array_merge($request->data(), ["uri" => ["base" => $request->getBase(), "chunks" => $request->getChunks()]]);

			// Bind response values.
			$this->output->bind([
				"T_REQUEST_DATA" => base64_encode(json_encode($reqData, JSON_NUMERIC_CHECK))

			])->finalize();
		}

		echo $this->output;

		exit;
	}


	protected function setup(){ /**/ }


	public function getRouter(){ return $this->router; }
	public function getRoute(){ return $this->route; }
	public function getConfig(){ return $this->config; }

}