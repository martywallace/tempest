<?php namespace Tempest\Base;

use Tempest\Base\Error;
use Tempest\Routing\Router;
use Tempest\Templating\Template;


/**
 * The core class of Tempest - the first to be initialized, managing configuration and router setup.
 * @author Marty Wallace.
 */
class Tempest
{

	private $config;
	private $router;
	private $mime = MIME_TEXT;
	private $output = '';
	private $errors = [];


	/**
	 * Start the application.
	 */
	public function start()
	{
		$this->config = new Config();
		$this->router = new Router();

		$this->router->register($this->config->data("routes"));
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
				"errors" => Template::load('/templates/tempest/error.html')->batch($this->errors)
			]);
		}
		
		$this->finalize();
	}


	/**
	 * Handles an error triggered by the application. Errors are queued and presented together.
	 * @param $number The error number.
	 * @param $string The error text.
	 * @param $file The file triggering the error.
	 * @param $line The line number triggering the error.
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
		if(is_a($this->output, 'Tempest\Routing\Output'))
		{
			// Final output is an instance of <code>Tempest/Routing/Output</code> - get the final
			// output first, as well as a the relevant MIME type.
			$this->mime = $this->output->getMime();
			$this->output = $this->output->getFinalOutput($this);
		}

		head(["Content-type" => $this->mime]);
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