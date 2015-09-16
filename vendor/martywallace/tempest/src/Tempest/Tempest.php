<?php

namespace Tempest;

use Exception;
use Tempest\Rendering\Twig;
use Tempest\Routing\Router;


/**
 * Tempest's core, extended by your core application class.
 *
 * @property int $status The HTTP status to be sent back to the client.
 * @property-read string $root The framework root directory.
 * @property-read Twig $twig A reference to the inbuilt Twig component, used to render templates with Twig.
 * @property-read Router $router A reference to the Router component.
 *
 * @package Tempest
 * @author Marty Wallace
 */
abstract class Tempest extends Element implements IConfigurationProvider
{

    /** @var Tempest */
    private static $_instance;


    /**
     * Instantiate the application.
     * @param string $root The framework root directory.
     * @param string $configPath The application configuration file path, relative to the application root.
     * @return Tempest
     */
    public static function instantiate($root, $configPath = null)
    {
        if (self::$_instance === null)
        {
            self::$_instance = new static($root, $configPath);
        }

        return self::$_instance;
    }


    /** @var string */
    private $_root;

    /** @var Configuration */
    private $_config;

    /** @var int */
    private $_status = 200;


    /**
     * Constructor. Should not be called directly.
     * @see Tempest::instantiate() To create a new instance instead.
     * @param string $root The application root directory.
     * @param string $configPath The application configuration file path, relative to the application root.
     */
    public function __construct($root, $configPath = null)
    {
        $this->_root = $root;

        if ($configPath !== null)
        {
            // Initialize configuration.
            $this->_config = new Configuration($root . '/' . trim($configPath, '/'));
        }

        error_reporting($this->_config->dev ? E_ALL : 0);
    }


    public function __get($prop)
    {
        if ($prop === 'root') return $this->_root;
        if ($prop === 'status') return $this->_status;

        return parent::__get($prop);
    }


    public function __set($prop, $value)
    {
        if ($prop === 'status')
        {
            $this->_status = $value;

            if (function_exists('http_response_code')) http_response_code($value);
            else header('X-PHP-Response-Code: ' . $value, true, $value);
        }

        else parent::__set($prop, $value);
    }


    /**
     * Get application configuration data.
     * @param string $prop The configuration data to get.
     * @param mixed $fallback A fallback value to use if the specified data does not exist.
     * @return mixed
     */
    public function config($prop, $fallback = null)
    {
        if ($this->_config !== null)
        {
            return $this->_config->get($prop, $fallback);
        }

        return $fallback;
    }


    /**
     * Attempt to execute a block of code. If any exceptions are thrown in the attempted block, they will be caught and
     * displayed in Tempest's exception page.
     * @param callable $callable Block of code to attempt to execute.
     */
    private function _attempt($callable)
    {
        try
        {
            $callable();
        }
        catch (Exception $exception)
        {
            $this->status = 500;

            if ($this->_config->dev)
            {
                die($this->twig->render('@tempest/exception.html', array(
                    'exception' => $exception
                )));
            }
        }
    }


    /**
     * Start running the application.
     */
    public function start()
    {
        $this->_attempt(function() {
            $this->addComponent('twig', new Twig());
            $this->addComponent('router', new Router());

            $this->setup();
        });
    }


    /**
     * Set up the application.
     * @throws Exception
     */
    protected abstract function setup();

}