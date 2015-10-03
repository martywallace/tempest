<?php

namespace Tempest;

use Exception;
use Tempest\Http\ContentType;
use Tempest\Http\Response;
use Tempest\Services\FilesystemService;
use Tempest\Services\Service;
use Tempest\Services\TwigService;
use Tempest\Http\Router;
use Tempest\Http\Controller;

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

	    // TODO: Investigate correct procedures for clean session setup.
	    session_start();

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
     * Attempt to execute a block of code. If any exceptions are thrown in the attempted block, they will be caught and
     * displayed in Tempest's exception page.
     *
     * @param callable $callable Block of code to attempt to execute.
     */
    private function _attempt($callable) {
        try {
            $callable();
        } catch (Exception $exception) {
	        $response = new Response();
	        $response->status = 500;

            if ($this->dev) {
               $response->body = $this->twig->render('@tempest/500.html', [
                    'exception' => $exception
               ]);
            } else {
	            $response->contentType = ContentType::TEXT;
	            $response->body = 'App exception.';
            }

	        $response->send();
        }
    }

    /**
     * Start running the application.
     */
    public function start() {
        $this->_attempt(function() {
	        $services = array_merge([
		        'filesystem' => new FilesystemService(),
		        'twig' => new TwigService()
	        ], $this->bindServices());

            foreach ($services as $name => $component) {
	            $this->addService($name, $component);
            }

	        foreach ($this->bindControllers() as $controller) {
		        foreach ($controller->bindRoutes() as $route => $detail) {
			        $handler = $detail[1];

			        if (method_exists($controller, $handler)) {
				        $this->_router->add($detail[0], $route, array($controller, $handler));
			        } else {
				        throw new Exception('Method "' . $handler . '" does not exist on controller "' . get_class($controller) . '".');
			        }
		        }
	        }

	        $this->_router->dispatch();
        });
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
	protected abstract function bindServices();

	/**
	 * Defines the list of Controllers to be bound to the application at startup.
	 *
	 * @return Controller[]
	 */
	protected abstract function bindControllers();

}