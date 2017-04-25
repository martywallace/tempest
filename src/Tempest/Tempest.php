<?php

namespace Tempest;

use Exception;
use Tempest\Http\Status;
use Tempest\Services\FilesystemService;
use Tempest\Services\TwigService;
use Tempest\Services\SessionService;
use Tempest\Services\MemoizeService;
use Tempest\Http\Router;
use Tempest\Http\Response;
use Tempest\Utils\JSONUtil;
use Tempest\Utils\ObjectUtil;

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
 * @property-read string $host The value provided by the server name property on the web server.
 * @property-read string $port The port on which the application is running.
 * @property-read bool $secure Attempts to determine whether the application is running over SSL.
 *
 * @property-read TwigService $twig The inbuilt Twig service, used to render templates.
 * @property-read FilesystemService $filesystem The inbuilt service dealing with the filesystem.
 * @property-read SessionService $session The inbuilt service dealing with user sessions.
 * @property-read MemoizeService $memoization The inbuilt service dealing with memoization.
 *
 * @package Tempest
 * @author Marty Wallace
 */
abstract class Tempest {

	const DUMP_JSON = 'json';
	const DUMP_PRINT_R = 'print_r';
	const DUMP_VAR_DUMP = 'var_dump';

	/** @var Tempest */
	private static $_instance;

	/**
	 * Instantiate the application.
	 *
	 * @param string $root The framework root directory.
	 * @param string|array $config The application configuration. This can either be provided as a file path relative to
	 * the application root, or the configuration array directly.
	 * @param string|callable $http The application middleware and route declaration method. This can either be provided
	 * as a file path relative to the application root, or the method directly.
	 *
	 * @return static
	 */
	public static function instantiate($root, $config = null, $http = null) {
		if (empty(self::$_instance))  {
			self::$_instance = new static($root, $config, $http);
		}

		return self::$_instance;
	}

	/** @var string */
	private $_root;

	/** @var callable */
	private $_http;

	/** @var array */
	private $_config = [];

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
	 * @internal
	 *
	 * @see Tempest::instantiate() To create a new instance instead.
	 *
	 * @param string $root
	 * @param string|array
	 * @param string|callable
	 *
	 * @throws Exception If the $http parameter is not a valid type.
	 */
	public function __construct($root, $config = null, $http = null) {
		Environment::load($root);

		$this->_root = $root;

		if ($config !== null) {
			if (is_array($config)) $this->_config = $config;
			else $this->_config = require($this->root . '/' . trim($config, '/'));
		}

		if ($http !== null) {
			if (is_callable($http)) $this->_http = $http;
			else if (is_string($http)) $this->_http = require($this->root . '/' . trim($http, '/'));
			else throw new Exception(static::class . '::instantiate() requires a string or callable for parameter $http.');
		}

		date_default_timezone_set($this->timezone);
		error_reporting($this->dev ? E_ALL : 0);
	}

	public function __get($prop) {
		if ($prop === 'dev') return Environment::getBool('dev');
		if ($prop === 'enabled') return $this->config('enabled', true);

		if ($prop === 'url') {
			return $this->memoization->cache(static::class, 'url', function() {
				// Attempt to guess the website URL based on whether the request was over HTTPS, the serverName variable and
				// the port the request was made over.
				$guess = ($this->secure ? 'https://' : 'http://') .
					$_SERVER['SERVER_NAME'] .
					($this->port === 80 || $this->port === 443 ? '' : ':' . $this->port);

				return rtrim($this->config('url', $guess), '/');
			});
		}

		if ($prop === 'public') {
			return $this->memoization->cache(static::class, 'public', function() {
				$path = '/' . trim(parse_url($this->url, PHP_URL_PATH), '/');
				return $path === '/' ? '' : $path;
			});
		}

		if ($prop === 'root') return rtrim($this->_root, '/');
		if ($prop === 'router') return $this->_router;
		if ($prop === 'host') return $_SERVER['SERVER_NAME'];
		if ($prop === 'port') return intval($_SERVER['SERVER_PORT']);
		if ($prop === 'timezone') return $this->config('timezone', @date_default_timezone_get());

		if ($prop === 'secure') {
			return $this->memoization->cache(static::class, 'secure', function() {
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
	public function config($prop = null, $fallback = null) {
		if ($prop === null) return $fallback === null ? $this->_config : $fallback;

		return ObjectUtil::getDeepValue($this->_config, $prop, $fallback);
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
	 * Run the application.
	 */
	public function run() {
		try {
			$customServices = $this->services();

			if (empty($customServices) || !is_array($customServices)) {
				$customServices = [];
			}

			$services = array_merge([
				// Services that the core depends on.
				'filesystem' => FilesystemService::class,
				'twig' => TwigService::class,
				'session' => SessionService::class,
				'memoization' => MemoizeService::class
			], $customServices);

			foreach ($services as $name => $service) {
				$this->addService($name, $service);
			}

			// Set up the application after services are bound.
			$this->setup();

			if ($this->enabled) {
				$router = new Router();

				if (!empty($this->_http)) {
					if (is_callable($this->_http)) $router->add($this->_http);
					else throw new Exception('The HTTP handler must be a function.');
				}

				$router->dispatch();
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