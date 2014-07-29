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
	private $mime = 'text/plain';
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

		$request = $this->router->getRequest();
		$match = $this->router->getMatch();

		if($match !== null)
		{
			$class = preg_replace('/\.+/', '\\', 'Responses\\' . $match->getHandlerClass());
			$method = $match->getHandlerMethod();
			$response = new $class($this);

			if(method_exists($response, $method))
			{
				$response->setup($request);
				$this->output = $response->$method($request);
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
			trigger_error("404: Input route <code>{$request}</code> not handled.");
		}
		
		if(count($this->errors) > 0)
		{
			$row = new Template('<tr><td>{{ key }}</td><td>{{ value }}</td></tr>');

			// Errors found, use error output.
			$this->output = Template::load('/templates/tempest/shell.html')->bind([
				"title" => "Application Error",
				"version" => TEMPEST_VERSION,
				"uri" => $request,
				"get" => $row->copy()->batch($request->data(GET)),
				"post" => $row->copy()->batch($request->data(POST)),
				"named" => $row->copy()->batch($request->data(NAMED)),
				"content" => Template::load('/templates/tempest/errors.html')->bind([
					"errors" => Template::load('/templates/tempest/error-item.html')->batch($this->errors)
				])
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
		$error = new Error($number, $string, $file, $line, $context);
		$this->errors[] = $error;
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