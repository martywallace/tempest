<?php namespace Tempest\Base;

use Tempest\Routing\Router;
use Tempest\Base\Error;
use Tempest\Templating\Template;


/**
 * The core class of Tempest - the first to be initialized, managing configuration and router setup.
 * @author Marty Wallace.
 */
class Tempest
{

	private $config;
	private $router;
	private $mime;
	private $output = '';
	private $errors = [];


	/**
	 * Start the application.
	 */
	public function start()
	{
		$this->config = new Config();
		$this->router = new Router();

		$this->router->register($this->config->getData()["routes"]);
		$this->setup();

		$match = $this->router->getMatch();

		if($match !== null)
		{
			$class = preg_replace('/\.+/', '\\', 'Responses\\' . $match->getHandlerClass());
			$method = $match->getHandlerMethod();
			$response = new $class($this);

			if(method_exists($response, $method))
			{
				$response->setup($this->router->getRequest());
				$this->output = $response->$method($this->router->getRequest());
				$this->mime = $response->getMime();
			}
			else
			{
				// Response class was constructed successfully, but the target method was not defined.
				trigger_error("Response class <code>$class</code> does not have the method <code>$method()</code>.");
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
			$this->output = Template::load('/templates/tempest/errors.html')->bind([
				"errors" => Template::batch(Template::load('/templates/tempest/error.html'), $this->errors)
			]);

			$this->mime = MIME_HTML;
		}
		
		$this->finalize();
	}


	/**
	 * Handles an error triggered by the application. Errors are queued and presented together.
	 * @param $number The line number triggering the error.
	 * @param $string The error text.
	 * @param $file The file triggering the error.
	 * @param $context The error context.
	 */
	public function error($number, $string, $file, $line, $context)
	{
		$this->errors[] = new Error($number, $string, $file, $line, $context);
	}


	/**
	 * Finalize the application setup.
	 */
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


	/**
	 * Called by <code>start()</code> after the configuration and router have been initialized.
	 * Override  for custom initialization logic in <code>Application</code>.
	 */
	protected function setup(){ /**/ }


	/**
	 * Returns the active <code>Router</code> instance.
	 */
	public function getRouter(){ return $this->router; }


	/**
	 * Returns the active <code>Config</code> instance.
	 */
	public function getConfig(){ return $this->config; }

}