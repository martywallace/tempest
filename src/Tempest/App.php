<?php namespace Tempest;

use Exception;
use Closure;
use Tempest\Events\{AppEvent, ExceptionEvent, HttpKernelEvent};
use Tempest\Http\{Http, Request, Response};
use Tempest\Services\{Database, Twig, Session};

/**
 * The core application class, from which your own core application class extends. The App class is responsible for
 * bootstrapping your services and configuration.
 *
 * @property-read string $root The application root directory - the result of moving on directory up from the value
 * provided to {@link App::boot()}. Always without a trailing slash.
 * @property-read bool $dev Whether or not the application is in development mode.
 *
 * @property-read Database $db The inbuilt database service.
 * @property-read Twig $twig The inbuilt Twig service, used to render Twig templates.
 * @property-read Session $session The inbuilt session handling service.
 *
 * @author Marty Wallace
 */
abstract class App extends Container {

	/** The framework version. */
	const VERSION = '5.0.0';

	/** @var static */
	protected static $_instance;

	/**
	 * Create and boot up an application instance.
	 *
	 * @param string $root The application root directory, usually one level above the webroot (or wherever your
	 * composer.json sits).
	 * @param Closure|array|string $config Application configuration. Can either be provided as a raw configuration
	 * array, a closure providing the configuration array or a path pointing to a file relative to the application root
	 * that provides a configuration array in either of the first two formats. In the case of a closure, it will be
	 * provided with the {@link Environment}.
	 *
	 * @return static
	 *
	 * @throws Exception If the application has already been booted.
	 */
	public static function boot($root, $config = null) {
		if (!empty(static::$_instance)) {
			throw new Exception('The application has already been booted.');
		}

		static::$_instance = new static();

		// We use an alternate private method call instead of the constructor so that calls to App::get() don't throw an
		// exception (as static::$_instance would be null until after the constructor resolved).
		static::$_instance->_setup($root, $config);

		return static::$_instance;
	}

	/**
	 * Statically get the application instance. {@link App::boot()} must be called before this.
	 *
	 * @return static
	 *
	 * @throws Exception If the application was not previously {@link App::boot() booted}.
	 */
	public static function get() {
		if (empty(static::$_instance)) {
			throw new Exception('Missing preceding call to App::boot().');
		}

		return static::$_instance;
	}

	/** @var string */
	private $_root;

	/** @var Environment */
	private $_environment;

	/** @var array */
	private $_config;

	protected function __construct() {
		$this->addServices([
			'db' => Database::class,
			'twig' => Twig::class,
			'session' => Session::class
		]);

		parent::__construct();
	}

	/**
	 * @param string $root
	 * @param Closure|array|string $config
	 *
	 * @throws Exception
	 */
	private function _setup($root, $config) {
		$this->_root = rtrim($root, '/\\');
		$this->_environment = new Environment();

		if (!empty($config)) {
			if (is_string($config)) {
				$path = $this->_root . DIRECTORY_SEPARATOR . $config;

				if (!file_exists($path)) {
					throw new Exception('Configuration file "' . $path . '" does not exist.');
				} else {
					$config = require($path);
				}
			}

			if (is_array($config)) {
				// Raw configuration.
				$this->_config = $config;
			} else if (is_callable($config)) {
				$this->_config = $config($this->_environment);
			} else {
				throw new Exception('Configuration was provided in an unacceptable format.');
			}
		} else {
			$this->_config = [];
		}

		array_walk_recursive($this->_config, function($value, $key) {
			if (strpos($key, '.') !== false) {
				throw new Exception('Configuration fields cannot contain the "." character, as this is used for nested property querying.');
			}
		});

		$this->dispatch(AppEvent::SETUP);
		$this->setup();
	}

	public function __get($prop) {
		if ($prop === 'root') return $this->_root;
		if ($prop === 'dev') return $this->config('dev', false);

		return parent::__get($prop);
	}

	public function __isset($name) {
		return $this->{$name} !== null;
	}

	/**
	 * Gets a value stored in the application configuration.
	 *
	 * @param string $query The name of the property or a dot (.) delimited path to a descendant property to get.
	 * @param mixed $fallback A fallback value to provide if the configuration property was not found.
	 *
	 * @return mixed
	 */
	public function config($query = null, $fallback = null) {
		if ($query === null) return $this->_config;
		return Utility::dig($this->_config, $query, $fallback);
	}

	/**
	 * Called after all services are bound to the application.
	 *
	 * @return mixed
	 */
	abstract protected function setup();

	/**
	 * Handle an incoming HTTP request.
	 *
	 * @param Request $request A HTTP request made to the application.
	 * @param Closure|string $routes Known routes to match the request against. Can either be a function accepting a
	 * {@link Http HTTP instance} or a string pointing to a PHP file that returns a function accepting a HTTP instance.
	 *
	 * @return Response
	 */
	public function http(Request $request, $routes = null) {
		$kernel = new Http($routes);

		$kernel->addListener(ExceptionEvent::EXCEPTION, function(ExceptionEvent $event) {
			$this->dispatch(ExceptionEvent::EXCEPTION, $event);
		});

		$this->dispatch(HttpKernelEvent::BOOTED, new HttpKernelEvent($kernel, $request));

		// Handle the request and generate a response.
		$response = $kernel->handle($request);

		$this->dispatch(HttpKernelEvent::RESPONSE_READY, new HttpKernelEvent($kernel, $request, $response));

		return $response;
	}

	/**
	 * Terminate the application.
	 */
	public function terminate() {
		$this->dispatch(AppEvent::TERMINATE);
		exit;
	}

}