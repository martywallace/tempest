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
use Tempest\Http\Controller;
use Tempest\Http\Response;

/**
 * Tempest's core, extended by your core application class.
 *
 * @property-read bool $dev Whether the application is in development mode.
 * @property-read string $url The public application URL, always without a trailing slash.
 * @property-read string $root The framework root directory, always without a trailing slash.
 * @property-read string $timezone The application timezone.
 *
 * @property-read Router $router The application router.
 * @property-read string $host The value provided by the server name property on the web server.
 * @property-read string $port The port on which the application is running.
 * @property-read bool $secure Attempts to determine whether the application is running over SSL.
 *
 * @property-read TwigService $twig The inbuilt Twig service, used to render templates.
 * @property-read FilesystemService $filesystem The inbuilt service dealing with the filesystem.
 * @property-read SessionService $sessions The inbuilt service dealing with user sessions.
 * @property-read DatabaseService $db The inbuilt service dealing with a database and its content.
 *
 * @package Tempest
 * @author Marty Wallace
 */
abstract class Tempest {

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
		if (self::$_instance === null)  {
			self::$_instance = new static($root, $configPath);
		}

		return self::$_instance;
	}

	/** @var Router */
	private $_router;

	/** @var string */
	private $_root;

	/** @var Configuration */
	private $_config;

	/** @var Service[] */
	private $_services = array();

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
			$this->_config = new Configuration($root . '/' . trim($configPath, '/'));
		}

		date_default_timezone_set($this->timezone);
		error_reporting($this->dev ? E_ALL : 0);
	}

	public function __get($prop) {
		// Settings provided by app configuration.
		if ($prop === 'dev') return $this->config('dev', false);

		if ($prop === 'url') {
			// Attempt to guess the website URL based on whether the request was over HTTPS, the serverName variable and
			// the port the request was made over.
			$guess = ($this->secure ? 'https://' : 'http://') .
				$_SERVER['SERVER_NAME'] .
				($this->port === 80 || $this->port === 443 ? '' : ':' . $this->port);

			return rtrim($this->config('url', $guess), '/');
		}

		if ($prop === 'root') return rtrim($this->_root, '/');
		if ($prop === 'router') return $this->_router;

		// Useful server information.
		if ($prop === 'host') return $_SERVER['SERVER_NAME'];
		if ($prop === 'port') return intval($_SERVER['SERVER_PORT']);

		if ($prop === 'secure') {
			return (!empty($_SERVER['HTTPS']) &&
				strtolower($_SERVER['HTTPS']) !== 'off') ||
				$this->port === 443;
		}

		if ($prop === 'timezone') {
			return $this->config('timezone', date_default_timezone_get());
		}

		if ($this->hasService($prop)) {
			// We found a service with a matching name.
			$service = $this->_services[$prop];

			if (!$service->setup) {
				$service->runSetup();
			}

			return $service;
		}

		return null;
	}

	public function __set($prop, $value) {
		//
	}

	public function __isset($prop) {
		return property_exists($this, $prop) ||
			$this->hasService($prop) ||
			$this->{$prop} !== null;
	}

	/**
	 * Get application configuration data.
	 *
	 * @param string $prop The configuration data to get.
	 * @param mixed $fallback A fallback value to use if the specified data does not exist.
	 *
	 * @return mixed
	 */
	public function config($prop, $fallback = null) {
		if ($this->_config !== null) {
			return $this->_config->get($prop, $fallback);
		}

		return $fallback;
	}

	/**
	 * Start running the application.
	 */
	public function start() {
		try {
			$services = array_merge(array(
				// Services that the core depends on.
				'filesystem' => new FilesystemService(),
				'twig' => new TwigService(),
				'session' => new SessionService(),
				'db' => new DatabaseService()
			), $this->bindServices());

			foreach ($services as $name => $service) {
				$this->addService($name, $service);
			}

			if (!empty($this->config('db'))) {
				// Bind models if a database connection is provided.
				$this->db->helper->map($this->bindModels());
			}

			foreach ($this->bindControllers() as $controller) {
				foreach ($controller->bindRoutes() as $route => $detail) {
					if (!is_array($detail) || count($detail) >= 2) {
						if (method_exists($controller, $detail[1])) {
							// Convert handler detail to full callable.
							$detail[1] = array($controller, $detail[1]);

							$this->_router->add(new Route($route, $detail));
						} else {
							throw new Exception('Method "' . $detail[1] . '" does not exist on controller "' . get_class($controller) . '".');
						}
					} else {
						throw new Exception('Invalid route detail supplied for route "' . $route . '".');
					}
				}
			}

			$this->_router->dispatch();
		} catch (Exception $exception) {
			// Application did not run correctly.
			$response = new Response(Status::INTERNAL_SERVER_ERROR);
			$response->body = app()->twig->render('@tempest/500.html', array('exception' => $exception));
			$response->send();
		}
	}

	/**
	 * Add a service to the application.
	 *
	 * @param string $name The name used to reference the service.
	 * @param Service $service The service to add.
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
	 * Defines the list of services to be bound to the application at startup.
	 *
	 * @return Service[]
	 */
	protected function bindServices() { return array(); }

	/**
	 * Defines the list of Controllers to be bound to the application at startup.
	 *
	 * @return Controller[]
	 */
	protected function bindControllers() { return array(); }

	/**
	 * Defines the list of models to be bound to the application at startup.
	 *
	 * @return string[]
	 */
	protected function bindModels() { return array(); }

}