<?php namespace Tempest;

use Exception;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Tempest\Events\{
	AppEvent, ExceptionEvent, HttpKernelEvent, ServiceEvent
};
use Tempest\Http\{Http, Request, Response};
use Tempest\Services\{Database, Twig};

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
 *
 * @author Marty Wallace
 */
abstract class App extends EventDispatcher {

	/** @var static */
	protected static $_instance;

	/**
	 * Create and boot up an application instance.
	 *
	 * @param string $root The application root directory, usually one level above the webroot.
	 * @param array|string $config Application configuration. Can either be provided as a raw configuration array, or as
	 * a string pointing to a configuration file relative to the provided root.
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

	/** @var array */
	private $_config;

	/** @var string[] */
	private $_services = [];

	/** @var Service[] */
	private $_serviceInstances = [];

	private function __construct() { }

	/**
	 * @param string $root
	 * @param array|string $config
	 */
	private function _setup($root, $config) {
		$this->_root = rtrim($root, '/\\');

		if (is_array($config)) $this->_config = $config;
		else if (is_string($config) && !empty($config)) $this->_config = require($this->_root . DIRECTORY_SEPARATOR . $config);
		else $this->_config = [];

		array_walk_recursive($this->_config, function($value, $key) {
			if (strpos($key, '.') !== false) {
				throw new Exception('Configuration fields cannot contain the "." character, as this is used for nested property querying.');
			}
		});

		$this->_services = array_merge([
			'db' => Database::class,
			'twig' => Twig::class
		], $this->services());

		$this->dispatch(AppEvent::SETUP);
		$this->setup();
	}

	public function __get($prop) {
		if ($prop === 'root') return $this->_root;
		if ($prop === 'dev') return $this->config('dev', false);

		// Search for a service.
		if ($this->hasService($prop)) return $this->getService($prop);

		return null;
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
	 * Declare all application services to be bound.
	 *
	 * @return string[]
	 */
	abstract protected function services();

	/**
	 * Called after all services are bound to the application.
	 *
	 * @return mixed
	 */
	abstract protected function setup();

	/**
	 * Add a service to the application.
	 *
	 * @param string $name The name used to reference the service within the application.
	 * @param string $service The class name of the service to add.
	 *
	 * @throws Exception If a service with the same name already exists.
	 */
	protected function addService($name, $service) {
		if ($this->hasService($name)) {
			throw new Exception('Service "' . $service . '" has already been added to the application.');
		}

		$this->_services[$name] = $service;
	}

	/**
	 * Force boot a service, instantiating it for future usage.
	 *
	 * @param string|string[] $names A name or array of names of services to boot.
	 *
	 * @throws Exception If an input service has already been booted.
	 */
	protected function bootServices($names) {
		if (!is_array($names)) $names = [$names];

		foreach ($names as $name) {
			if ($this->hasBootedService($name)) throw new Exception('Service "' . $name . '" has already been booted.');

			$instance = new $this->_services[$name]();

			$this->dispatch(ServiceEvent::BOOTED, new ServiceEvent($name, $instance));
			$this->_serviceInstances[$name] = $instance;
		}
	}

	/**
	 * Determine whether the application has a service with the specified name.
	 *
	 * @param string $name The service name.
	 *
	 * @return bool
	 */
	public function hasService($name) {
		return array_key_exists($name, $this->_services);
	}

	/**
	 * Determine whether the application has booted a service with the specified name.
	 *
	 * @param string $name The service name.
	 *
	 * @return bool
	 */
	public function hasBootedService($name) {
		return array_key_exists($name, $this->_serviceInstances);
	}

	/**
	 * Get a service. If the service has not been booted, it will be booted first.
	 *
	 * @param string $name The service name.
	 *
	 * @return Service
	 *
	 * @throws Exception If the service does not exist.
	 */
	public function getService($name) {
		if (!$this->hasService($name)) throw new Exception('Service "' . $name . '" does not exist.');
		if (!$this->hasBootedService($name)) $this->bootServices($name);

		return $this->_serviceInstances[$name];
	}

	/**
	 * Handle an incoming HTTP request.
	 *
	 * @param Request $request A HTTP request made to the application.
	 * @param callable|string $routes Known routes to match the request against. Can either be a function accepting a
	 * {@link Http HTTP instance} or a string pointing to a PHP file that returns a function accepting a HTTP instance.
	 *
	 * @return Response
	 */
	public function http(Request $request, $routes = null) {
		$kernel = new Http($routes);

		$kernel->addListener(ExceptionEvent::EXCEPTION, function(ExceptionEvent $event) {
			$this->dispatch(ExceptionEvent::EXCEPTION, $event);
		});

		$this->dispatch(HttpKernelEvent::BOOTED, new HttpKernelEvent($kernel));

		return $kernel->handle($request, $routes);
	}

	/**
	 * Terminate the application.
	 */
	public function terminate() {
		$this->dispatch(AppEvent::TERMINATE);
		exit;
	}

}