<?php namespace Tempest\Base;

use Tempest\Base\Error;
use Tempest\HTTP\Router;
use Tempest\HTTP\Status;
use Tempest\HTTP\Request;
use Tempest\Output\BaseOutput;
use Tempest\Templating\Template;


/**
 * The core class of Tempest - the first to be initialized, managing configuration and router setup.
 * @author Marty Wallace.
 */
class Tempest
{

	private $router;
	private $status = Status::OK;
	private $output = null;
	private $errors = array();


	/**
	 * Start the application.
	 */
	public function start()
	{
		$this->router = new Router();

		$this->setup($this->router);

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
				$this->setOutput($response->finalize($response->$method($request)));
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
			$this->status = Status::NOT_FOUND;
			trigger_error("Input route <code>{$request}</code> not handled.");
		}
		
		$this->finalize();
	}


	/**
	 * Abort the application.
	 * @param $code int The HTTP status code.
	 */
	public function abort($code = 400)
	{
		$this->status = $code;
		$this->finalize();
	}


	/**
	 * Handles an error triggered by the application. Errors are queued and presented together.
	 * @param $number int The error number.
	 * @param $string string The error text.
	 * @param $file string The file triggering the error.
	 * @param $line int The line number triggering the error.
	 * @param $context Array The error context.
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
		if($this->status <= 300 && count($this->errors) > 0)
		{
			// If the HTTP Response code is in the OK range but there are errors.
			$this->status = Status::INTERNAL_SERVER_ERROR;
		}

		if($this->status >= 300)
		{
			// If the HTTP Response code does not fall in the OK range, transform the output to an alternate value.
			// The alternate value is defined in App::showErrors().
			$this->setOutput($this->errorOutput($this->router->getRequest(), $this->status));
		}

		// Send the HTTP status, content-type and final output.
		header($_SERVER["HTTP_PROTOCOL"] . "$this->status", true, $this->status);
		header("Content-Type: {$this->output->getMime()}; charset={$this->output->getCharset()}");

		if($this->output !== null)
			echo $this->output->getFinalOutput($this, $this->router->getRequest());

		exit;
	}


	/**
	 * Defines alternate output for HTTP status codes that are not in the 2xx range.
	 * @param $request Request The request made.
	 * @param $code int The HTTP status code - used to determine what the result should be.
	 * @return string|Output The resulting output.
	 */
	protected function errorOutput(Request $request, $code)
	{
		if($code === Status::NOT_FOUND) return '404 - Not Found.';

		if($code >= 500)
		{
			// Server-side errors.
			return Template::load('/templates/tempest/shell.html')->bind(array(
				"title" => "Application Error",
				"version" => TEMPEST_VERSION,
				"uri" => $request,
				"get" => count($request->data(GET)) > 0 ? json_encode($r->data(GET), JSON_PRETTY_PRINT) : "-",
				"post" => count($request->data(POST)) > 0 ? json_encode($r->data(POST), JSON_PRETTY_PRINT) : "-",
				"named" => count($request->data(NAMED)) > 0 ? json_encode($r->data(NAMED), JSON_PRETTY_PRINT) : "-",
				"content" => Template::load('/templates/tempest/errors.html')->bind(array(
					"errors" => Template::load('/templates/tempest/error-item.html')->batch($this->errors)
				))
			));
		}

		return null;
	}


	/**
	 * Sets the application output. If the input is not an instance of BaseOutput, it will be converted to one.
	 * @param $value string|Output The output value.
	 */
	protected function setOutput($value)
	{
		if($value !== null && !is_a($value, 'Tempest\Output\BaseOutput'))
		{
			// Transform existing output to an output object, if not already.
			$value = new BaseOutput('text/plain', $value);
		}

		$this->output = $value;
	}


	/**
	 * Called by <code>start()</code> after the configuration and router have been initialized.
	 * Override  for custom initialization logic in <code>App</code>.
	 * @param $router Router The application router.
	 */
	protected function setup(Router $router){ /**/ }


	/**
	 * Returns the active <code>Router</code> instance.
	 * @return Router
	 */
	public function getRouter(){ return $this->router; }

}