<?php

namespace Tempest;

use Exception;
use Tempest\Http\Status;
use Tempest\Services\FilesystemService;
use Tempest\Services\TwigService;
use Tempest\Services\SessionService;
use Tempest\Http\Route;
use Tempest\Http\Router;
use Tempest\Http\Response;
use Tempest\Utils\JSONUtil;
use Tempest\Utils\Memoizer;

/**
 * Tempest's core, extended by your application class.
 *
 * @property-read bool $dev Whether the application is in development mode, which is determined by whether a
 * configuration option called "dev" is provided and true, or whether the current environment is "dev".
 * @property-read bool $enabled Whether the application is currently enabled.
 * @property-read string $url The public application URL, always without a trailing slash.
 * @property-read string $public The public facing root relative to the app domain, always without a trailing slash.
 * @property-read string $root The application root directory provided by the outer application when instantiating Tempest, always without a trailing slash.
 * @property-read string $timezone The application timezone.
 *
 * @property-read Router $router The application router.
 * @property-read string $host The value provided by the server name property on the web server.
 * @property-read string $port The port on which the application is running.
 * @property-read bool $secure Attempts to determine whether the application is running over SSL.
 *
 * @property-read TwigService $twig The inbuilt Twig service, used to render templates.
 * @property-read FilesystemService $filesystem The inbuilt service dealing with the filesystem.
 * @property-read SessionService $session The inbuilt service dealing with user sessions.
 *
 * @package Tempest
 * @author Marty Wallace
 */
abstract class Tempest extends Memoizer {

	const DUMP_JSON = 'json';
	const DUMP_PRINT_R = 'print_r';
	const DUMP_VAR_DUMP = 'var_dump';

	/** @var Tempest */
	private static $_instance;

	/**
	 * Instantiate the application.
	 *
	 * @param string $root The framework root directory.
	 * @param string $config The application configuration file path, relative to the application root.
	 *
	 * @return static
	 */
	public static function instantiate($root, $config = null) {
		if (empty(self::$_instance))  {
			self::$_instance = new static($root, $config);
		}

		return self::$_instance;
	}

	/** @var string */
	private $_root;

	/** @var Configuration */
	private $_config;

	/** @var Router */
	private $_router;

	/** @var string[] */
	private $_services = [];

	/** @var string[] */
	private $_setupServices = [];

	/**
	 * A static reference to Tempest.
	 *
	 * @return static
	 *
	 * @throws Exception If Tempest has not yet been instantiated.
	 */
	public static function get() {
		if (!empty(self::$_instance)) {
			return self::$_instance;
		} else {
			throw new Exception('You must instantiate Tempest before you can refer to it statically.');
		}
	}

	/**
	 * Constructor. Should not be called directly.
	 *
	 * @see Tempest::instantiate() To create a new instance instead.
	 *
	 * @param string $root The application root directory.
	 * @param string $config The application configuration file path, relative to the application root.
	 */
	public function __construct($root, $config = null) {
		Environment::load($root);

		$this->_root = $root;
		$this->_router = new Router();

		if ($config !== null) {
			// Initialize configuration.
			$this->_config = new Configuration($this->root . '/' . trim($config, '/'));
		}

		date_default_timezone_set($this->timezone);
		error_reporting($this->dev ? E_ALL : 0);
	}

	public function __get($prop) {
		if ($prop === 'dev') return Environment::getBool('dev');
		if ($prop === 'enabled') return $this->_config->get('enabled', true);

		if ($prop === 'url') {
			return $this->memoize('url', function() {
				// Attempt to guess the website URL based on whether the request was over HTTPS, the serverName variable and
				// the port the request was made over.
				$guess = ($this->secure ? 'https://' : 'http://') .
					$_SERVER['SERVER_NAME'] .
					($this->port === 80 || $this->port === 443 ? '' : ':' . $this->port);

				return rtrim($this->_config->get('url', $guess), '/');
			});
		}

		if ($prop === 'public') {
			return $this->memoize('public', function() {
				$path = '/' . trim(parse_url($this->url, PHP_URL_PATH), '/');
				return $path === '/' ? '' : $path;
			});
		}

		if ($prop === 'root') return rtrim($this->_root, '/');
		if ($prop === 'router') return $this->_router;
		if ($prop === 'host') return $_SERVER['SERVER_NAME'];
		if ($prop === 'port') return intval($_SERVER['SERVER_PORT']);
		if ($prop === 'timezone') return $this->_config->get('timezone', @date_default_timezone_get());

		if ($prop === 'secure') {
			return $this->memoize('secure', function() {
				return (!empty($_SERVER['HTTPS']) &&
					strtolower($_SERVER['HTTPS']) !== 'off') ||
					$this->port === 443;
			});
		}

		if ($this->hasService($prop)) {
			if (!array_key_exists($prop, $this->_setupServices)) {
				// First time being accessed, setup the service.
				$this->_setupServices[$prop] = new $this->_services[$prop]();
			}

			return $this->_setupServices[$prop];
		}

		return null;
	}

	public function __isset($prop) {
		return property_exists($this, $prop) || $this->hasService($prop) || $this->{$prop} !== null;
	}

	/**
	 * Get application configuration.
	 *
	 * @param string $prop The name of the configuration property to get.
	 * @param mixed $fallback The fallback value to return if the configuration property does not exist.
	 *
	 * @return mixed
	 */
	public function config($prop, $fallback = null) {
		return $this->_config->get($prop, $fallback);
	}

	/**
	 * Output data for debugging and stop the application.
	 *
	 * @see Tempest::DUMP_JSON
	 * @see Tempest::DUMP_PRINT_R
	 * @see Tempest::DUMP_VAR_DUMP
	 *
	 * @param mixed $data The data to debug.
	 * @param string $format The output format.
	 */
	public function dump($data, $format = self::DUMP_PRINT_R) {
		$format = strtolower($format);
		$class = gettype($data);

		if ($class === 'object') {
			$class = get_class($data);
		}

		$output = null;

		if ($format === self::DUMP_JSON) {
			$data = JSONUtil::encode($data, JSON_PRETTY_PRINT);
		} else {
			ob_start();

			if ($format === self::DUMP_PRINT_R) print_r($data);
			if ($format === self::DUMP_VAR_DUMP) var_dump($data);

			$data = ob_get_clean();
		}

		$response = new Response(Status::OK, static::get()->twig->render('@tempest/dump.html', [
			'data' => $data,
			'format' => $format,
			'class' => $class,
			'stack' => debug_backtrace()
		]));

		$response->send();
	}

	/**
	 * Start running the application.
	 */
	public function start() {
		try {
			if ($this->enabled) {
				$customServices = $this->services();

				if (empty($customServices) || !is_array($customServices)) {
					$customServices = [];
				}

				$services = array_merge([
					// Services that the core depends on.
					'filesystem' => FilesystemService::class,
					'twig' => TwigService::class,
					'session' => SessionService::class
				], $customServices);

				foreach ($services as $name => $service) {
					$this->addService($name, $service);
				}

				// Set up the application after services are bound.
				$this->setup();

				$routes = $this->_config->get('routes', []);

				if (!empty($routes)) {
					if (is_string($routes)) {
						// Load routes from an additional configuration file.
						if ($this->filesystem->exists($routes)) {
							$routes = $this->filesystem->import($routes);
						} else {
							throw new Exception('External routes could not be found at "' . $routes . '".');
						}
					}

					foreach ($routes as $definition) {
						$this->_router->add(new Route($definition));
					}
				} else {
					// No routes defined - always falling back to the templates directory.
					// ...
				}

				$this->_router->dispatch();
			} else {
				// Site is not enabled.
				$response = new Response(Status::SERVICE_UNAVAILABLE);
				$response->send();
			}
		} catch (Exception $exception) {
			$this->onException($exception);
		}
	}

	/**
	 * Add a service to the application.
	 *
	 * @param string $name The name used to reference the service.
	 * @param string $service The class name of the service to add.
	 *
	 * @throws Exception If a service with the provided name already exists.
	 */
	public function addService($name, $service) {
		if (!$this->hasService($name)) {
			$this->_services[$name] = $service;
		} else {
			throw new Exception('A service named "' . $name . '" already exists.');
		}
	}

	/**
	 * Determine whether or not a service with the specified name exists.
	 *
	 * @param string $name The name to check.
	 *
	 * @return bool
	 */
	public function hasService($name) {
		return array_key_exists($name, $this->_services);
	}

	/**
	 * This method runs if an exception was thrown by the application. It can be overridden in your own application
	 * class for custom error handling (e.g. you could palm the errors off to Slack for review).
	 *
	 * @param Exception $exception The exception that was thrown.
	 */
	protected function onException(Exception $exception) {
		$response = new Response(Status::INTERNAL_SERVER_ERROR, static::get()->twig->render('@tempest/_errors/500.html', ['exception' => $exception]));
		$response->send();
	}

	/**
	 * Defines the list of services to be bound to the application at startup.
	 *
	 * @return string[]
	 */
	abstract protected function services();

	/**
	 * Additional application setup, run after services are bound.
	 *
	 * @return mixed
	 */
	abstract protected function setup();

}