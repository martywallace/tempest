<?php

namespace Tempest;

use Exception;
use Tempest\Http\Status;
use Tempest\Services\Service;
use Tempest\Services\FilesystemService;
use Tempest\Services\TwigService;
use Tempest\Services\SessionService;
use Tempest\Services\DatabaseService;
use Tempest\Http\Route;
use Tempest\Http\Router;
use Tempest\Http\Response;
use Tempest\Utils\Memoizer;

/**
 * Tempest's core, extended by your application class.
 *
 * @property-read bool $dev Whether the application is in development mode.
 * @property-read bool $enabled Whether the application is currently enabled.
 * @property-read string $url The public application URL, always without a trailing slash.
 * @property-read string $public The public facing root relative to the app domain, always without a trailing slash.
 * @property-read string $root The application root directory provided by the outer application when instantiating Tempest, always without a trailing slash.
 * @property-read string $timezone The application timezone.
 * @property-read string $environment Alias for {@link Environment::current()}.
 *
 * @property-read Router $router The application router.
 * @property-read string $host The value provided by the server name property on the web server.
 * @property-read string $port The port on which the application is running.
 * @property-read bool $secure Attempts to determine whether the application is running over SSL.
 *
 * @property-read Configuration $config The configuration service, used to get config data.
 * @property-read TwigService $twig The inbuilt Twig service, used to render templates.
 * @property-read FilesystemService $filesystem The inbuilt service dealing with the filesystem.
 * @property-read SessionService $session The inbuilt service dealing with user sessions.
 * @property-read DatabaseService $db The inbuilt service dealing with a database and its content.
 *
 * @package Tempest
 * @author Marty Wallace
 */
abstract class Tempest extends Memoizer {

	/** @var Tempest */
	private static $_instance;

	/**
	 * Instantiate the application.
	 *
	 * @param string $root The framework root directory.
	 * @param string $configPath The application configuration file path, relative to the application root.
	 *
	 * @return Tempest
	 */
	public static function instantiate($root, $configPath = null) {
		if (empty(self::$_instance))  {
			self::$_instance = new static($root, $configPath);
		}

		return self::$_instance;
	}

	/** @var string */
	private $_root;

	/** @var Configuration */
	private $_config;

	/** @var Router */
	private $_router;

	/** @var Service[] */
	private $_services = array();

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
	 * @param string $configPath The application configuration file path, relative to the application root.
	 */
	public function __construct($root, $configPath = null) {
		$this->_root = $root;
		$this->_router = new Router();

		if ($configPath !== null) {
			// Initialize configuration.
			$this->_config = new Configuration($this->root . '/' . trim($configPath, '/'));
		}

		date_default_timezone_set($this->timezone);
		error_reporting($this->dev ? E_ALL : 0);
	}

	public function __get($prop) {
		if ($prop === 'dev') return Environment::current() === Environment::DEV;
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
		if ($prop === 'environment') return Environment::current();

		// Useful server information.
		if ($prop === 'host') return $_SERVER['SERVER_NAME'];
		if ($prop === 'port') return intval($_SERVER['SERVER_PORT']);

		if ($prop === 'secure') {
			return $this->memoize('secure', function() {
				return (!empty($_SERVER['HTTPS']) &&
					strtolower($_SERVER['HTTPS']) !== 'off') ||
					$this->port === 443;
			});
		}

		if ($prop === 'timezone') {
			return $this->_config->get('timezone', @date_default_timezone_get());
		}

		if ($prop === 'config') return $this->_config;

		if ($this->hasService($prop)) {
			// We found a service with a matching name. Set it up and return it.
			$service = $this->_services[$prop];
			$service->runSetup();

			return $service;
		}

		return null;
	}

	public function __isset($prop) {
		return property_exists($this, $prop) ||
			$this->hasService($prop) ||
			$this->{$prop} !== null;
	}

	/**
	 * Output some data for debugging and stop the application.
	 *
	 * @param mixed $data The data to debug.
	 * @param string $format The output format.
	 */
	public function dump($data, $format = 'print_r') {
		$format = strtolower($format);
		$output = null;

		if ($format === 'json') {
			$data = json_encode($data, JSON_PRETTY_PRINT);
		} else {
			ob_start();

			if ($format === 'print_r') print_r($data);
			if ($format === 'var_dump') var_dump($data);

			$data = ob_get_clean();
		}

		echo static::get()->twig->render('@tempest/dump.html', array(
			'data' => $data
		));

		exit;
	}

	/**
	 * Start running the application.
	 */
	public function start() {
		try {
			if ($this->enabled) {
				$customServices = $this->bindServices();

				if (empty($customServices) || !is_array($customServices)) {
					$customServices = array();
				}

				$services = array_merge(array(
					// Services that the core depends on.
					'filesystem' => new FilesystemService(),
					'twig' => new TwigService(),
					'session' => new SessionService(),
					'db' => new DatabaseService()
				), $customServices);

				foreach ($services as $name => $service) {
					$this->addService($name, $service);
				}

				// Set up the application after services are bound.
				$this->setup();

				$routes = $this->_config->get('routes', array());

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
	 * @param Service $service The service to add.
	 *
	 * @return Service|null
	 *
	 * @throws Exception
	 */
	public function addService($name, Service $service) {
		if (!$this->hasService($name)) {
			$this->_services[$name] = $service;
			return $service;
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
		$response = new Response(Status::INTERNAL_SERVER_ERROR, static::get()->twig->render('@tempest/500.html', array('exception' => $exception)));
		$response->send();
	}

	/**
	 * Defines the list of services to be bound to the application at startup.
	 *
	 * @return Service[]
	 */
	abstract protected function bindServices();

	/**
	 * Additional application setup, run after services are bound.
	 *
	 * @return mixed
	 */
	abstract protected function setup();

}