<?php namespace Tempest;

use Tempest\HTTP\Router;
use Tempest\HTTP\Status;
use Tempest\HTTP\Request;
use Tempest\HTTP\Controller;
use Tempest\HTTP\Response;
use Tempest\MySQL\Database;
use Tempest\Twig\Twig;
use Tempest\Utils\Path;
use Tempest\Services\PlatformService;


/**
 * The core class of Tempest - the first to be initialized, managing configuration and router setup.
 * @author Marty Wallace.
 */
class Tempest
{

	/**
	 * @var Tempest
	 */
	protected static $instance;


	/**
	 * Gets the current Tempest instance.
	 * @return Tempest
	 */
	public static function getInstance()
	{
		return static::$instance;
	}


	/**
	 * @var Router
	 */
	private $router;

	/**
	 * @var Path
	 */
	private $root;

	/**
	 * @var Config
	 */
	private $config;

	/**
	 * @var int
	 */
	private $status = Status::OK;

	/**
	 * @var Response
	 */
	private $response = null;

	/**
	 * @var Error[]
	 */
	private $errors = array();

	/**
	 * @var IService[]
	 */
	private $services = array();


	/**
	 * Constructor.
	 */
	public function __construct()
	{
		$this->config = new Config('general');

		error_reporting($this->config('dev', false) ? -1 : 0);
		date_default_timezone_set($this->config('timezone', 'Australia/Sydney'));
		set_error_handler(array($this, 'error'));

		static::$instance = $this;
	}


	/**
	 * Start the application.
	 */
	public function start()
	{
		$routes = new Config('routes');
		
		$this->services = array_merge($this->defineServices(), array(
			'twig' => new Twig(),
			'db' => new Database(),
			'platform' => new PlatformService()
		));

		$this->router = new Router();
		$this->router->register($routes->data());
		$this->setup();

		$request = $this->router->getRequest();
		$match = $this->router->getMatch();

		if ($match !== null)
		{
			// Create the response class.
			$controller = Controller::create(preg_replace('/\.+/', '\\', 'Controllers\\' . $match->getControllerClass()), $this);
			$method = $match->getControllerAction();

			if ($controller !== null)
			{
				if (method_exists($controller, $method))
				{
					$controller->setup($request, $match->getDetail());
					$this->setResponse($controller->finalize($controller->$method($request, $match->getDetail())));
				}
				else
				{
					// Responder class was constructed successfully, but the target method was not defined.
					trigger_error("Controller <code>" . get_class($controller) . "</code> does not have the method <code>$method()</code>.");
				}
			}
		}
		else
		{
			// Let's peek at what templates are available in <code>/html/</code>. If we find one with a name that
			// matches the request, let's use it!
			$uri = Path::create(REQUEST_URI)
				->relativeTo($this->getRoot())
				->setStrategy(Path::DELIMITER_LEFT)
				->value();

			$template = $uri === '/' ? 'home' : trim(preg_replace(Path::PATTERN_SLASHES, '-', $uri), '-');
			$twigResponse = $this->twig->render($template . '.html');

			if ($twigResponse !== null)
			{
				// Found a template that might work.
				$this->setResponse($twigResponse);
			}
			else
			{
				// No matching routes or templates.
				$this->status = Status::NOT_FOUND;
				trigger_error("Input route <code>{$request}</code> not handled.");
			}
		}
		
		$this->finalize();
	}


	/**
	 * Returns configuration data.
	 *
	 * @param string $prop The configuration property to get.
	 * @param mixed $fallback A fallback value to use if the property is not found.
	 *
	 * @return mixed
	 */
	public function config($prop = null, $fallback = null)
	{
		return $this->config->data($prop, $fallback);
	}


	/**
	 * Called by <code>start()</code> after the configuration and router have been initialized.
	 * Override for custom initialization logic in your application class.
	 */
	protected function setup(){ /**/ }


	/**
	 * Defines the services used by the application.
	 * Override in your application class to define additional services.
	 *
	 * @return array
	 */
	protected function defineServices()
	{
		return array(
			//
		);
	}


	/**
	 * Abort the application.
	 *
	 * @param $code int The HTTP status code.
	 */
	public function abort($code = 400)
	{
		$this->status = $code;
		$this->finalize();
	}


	/**
	 * Handles an error triggered by the application. Errors are queued and presented together.
	 *
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
		if ($this->status <= 300 && count($this->errors) > 0)
		{
			// If the HTTP response code is in the OK range but there are errors.
			$this->status = Status::INTERNAL_SERVER_ERROR;
		}

		if ($this->status >= 300)
		{
			// If the HTTP Responder code does not fall in the OK range, transform the output to an alternate value.
			// The alternate value is defined in App::showErrors().
			$this->setResponse($this->errorOutput($this->router->getRequest(), $this->status));
		}

		$this->setStatus($this->status);
		header("Content-Type: {$this->response->getMime()}; charset={$this->response->getCharset()}");

		if ($this->response !== null)
		{
			echo $this->response->getFinalOutput($this->router->getRequest());
		}

		exit;
	}


	/**
	 * Defines alternate output for HTTP status codes that are not in the 2xx range.
	 *
	 * @param $request Request The request made.
	 * @param $code int The HTTP status code - used to determine what the result should be.
	 *
	 * @return string|array|Response The resulting output.
	 */
	private function errorOutput(Request $request, $code)
	{
		if (Path::create(APP_ROOT . 'html/_status/' . $code . '.html')->isFile())
		{
			if ($code >= 500 && !$this->config('dev', false))
			{
				// Don't show server errors unless dev mode is turned on.
				return null;
			}

			// We can create Twig templates to render errors specific to certain HTTP status codes.
			return $this->twig->render('_status/' . $code . '.html', array(
				'errors' => $this->errors
			));
		}

		return null;
	}


	/**
	 * Magic getter. Attempts to return a Service if a property was not found with the specified name.
	 *
	 * @param string $prop
	 *
	 * @return IService
	 */
	public function __get($prop)
	{
		if (array_key_exists($prop, $this->services))
		{
			// Returns a service with the name.
			return $this->services[$prop];
		}

		return null;
	}


	/**
	 * Sets the application response. If the input is not an instance of Response, it will be converted to one.
	 * If the input is an array, it will be converted to JSON.
	 *
	 * @param $value string|array|Response The Response or primitive output value.
	 */
	protected function setResponse($value)
	{
		if (is_array($value))
		{
			// Transform returned array into JSON.
			$value = new Response('application/json', json_encode($value, JSON_NUMERIC_CHECK));
		}

		if (!is_a($value, 'Tempest\HTTP\Response'))
		{
			// Transform existing output to an output object, if not already.
			$value = new Response('text/plain', $value);
		}

		$this->response = $value;
	}


	/**
	 * Returns the active Router.
	 *
	 * @return Router
	 */
	public function getRouter(){ return $this->router; }


	/**
	 * Returns the client-side application root.
	 *
	 * @return Path
	 */
	public function getRoot()
	{
		if ($this->root === null)
		{
			$this->root = Path::create(
				$this->config('root', '/'),
				Path::DELIMITER_LEFT
			);
		}

		return $this->root;
	}


	/**
	 * Sends a HTTP status code.
	 * @param number $code The status code.
	 */
	public function setStatus($code)
	{
		if (function_exists('http_response_code')) http_response_code($this->status);
		else header('X-PHP-Response-Code: '. $this->status, true, $this->status);
	}


	/**
	 * Returns the list of defined services.
	 *
	 * @param bool $twigOnly Whether or not to only fetch services that are accessible in Twig.
	 *
	 * @return IService[]
	 */
	public function getServices($twigOnly = false)
	{
		if ($twigOnly)
		{
			$output = array();
			foreach ($this->services as $serviceName => $service)
			{
				if ($service->isTwigAccessible())
				{
					// This service is marked as being accessible within Twig templates.
					$output[$serviceName] = $service;
				}
			}

			return $output;
		}

		return $this->services;
	}


	/**
	 * Returns the current version of Tempest.
	 *
	 * @return string
	 */
	public function getVersion() { return '1.3.0'; }

}