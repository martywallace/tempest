<?php

namespace Tempest;

use Exception;
use Slim\App;
use Tempest\Components\Component;
use Tempest\Components\TwigComponent;
use Tempest\Routing\Controller;


/**
 * Tempest's core, extended by your core application class.
 *
 * @property-read string $root The framework root directory.
 * @property-read TwigComponent $twig A reference to the inbuilt Twig component, used to render templates with Twig.
 *
 * @package Tempest
 * @author Marty Wallace
 */
abstract class Tempest extends App {

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

    /** @var string */
    private $_root;

    /** @var Configuration */
    private $_config;

	/** @var Component[] */
	private $_components = [];

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

        if ($configPath !== null) {
            // Initialize configuration.
            $this->_config = new Configuration($root . '/' . trim($configPath, '/'));
        }

        error_reporting($this->_config->dev ? E_ALL : 0);

	    parent::__construct();
    }

    public function __get($prop) {
        if ($prop === 'root') return rtrim($this->_root . '/');
        if ($prop === 'status') return $this->_status;

	    if ($this->hasComponent($prop)) {
		    // We found a component with a matching name.
		    return $this->_components[$prop];
	    }

        return parent::__get($prop);
    }

    public function __set($prop, $value) {
        //
    }

	public function __isset($prop) {
		return property_exists($this, $prop) ||
			$this->hasComponent($prop) ||
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
    private function _attempt($callable)
    {
        try {
            $callable();
        }
        catch (Exception $exception) {
            $this->response->withStatus(500);

            if ($this->_config->dev) {
               die($this->twig->render('@tempest/exception.html', array(
                    'exception' => $exception
               )));
            }
        }
    }

    /**
     * Start running the application.
     */
    public function start() {
        $this->_attempt(function() {
	        $components = array_merge(array(
		        'twig' => new TwigComponent()
	        ), $this->bindComponents());

            foreach ($components as $name => $component) {
	            $this->addComponent($name, $component);
            }

	        foreach ($this->bindControllers() as $controller) {
		        foreach ($controller->bindRoutes() as $route => $detail) {
			        if (count($detail) === 2) {
				        $method = strtoupper($detail[0]);
				        $this->map([$method], $route, array($controller, $detail[1]));
			        } else {
				        throw new Exception('Invalid route definition bound to "' . $route . '".');
			        }
		        }
	        }

	        $this->run();
        });
    }

	/**
	 * Add a Component to the application.
	 *
	 * @param string $name The name used to reference the Component.
	 * @param Component $component The Component to add.
	 * @return Component|null
	 *
	 * @throws Exception
	 */
	public function addComponent($name, Component $component) {
		if (!$this->hasComponent($name)) {
			$this->_components[$name] = $component;
			return $component;
		} else {
			throw new Exception('A Component named "' . $name . '" already exists on this Element.');
		}
	}

	/**
	 * Determine whether or not a Component with the specified name exists.
	 *
	 * @param string $name The name to check.
	 *
	 * @return bool
	 */
	public function hasComponent($name) {
		return array_key_exists($name, $this->_components);
	}

	/**
	 * Defines the list of Components to be bound to the application at startup.
	 *
	 * @return Component[]
	 */
	protected abstract function bindComponents();

	/**
	 * Defines the list of Controllers to be bound to the application at startup.
	 *
	 * @return Controller[]
	 */
	protected abstract function bindControllers();

}