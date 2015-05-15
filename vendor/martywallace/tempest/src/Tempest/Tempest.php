<?php

namespace Tempest;

use Exception;


/**
 * Tempest's core, extended by your core application class.
 *
 * @property-read string $root The framework root directory.
 *
 * @package Tempest
 *
 * @author Marty Wallace
 */
abstract class Tempest
{

    /** @var Tempest */
    private static $_instance;


    /**
     * Instantiate the application.
     *
     * @param string $root The framework root directory.
     * @param array $autoloadPaths A list of paths to attempt to autoload classes from.
     *
     * @return Tempest
     */
    public static function instantiate($root, Array $autoloadPaths = null)
    {
        if (self::$_instance === null)
        {
            self::$_instance = new static($root);

            foreach ($autoloadPaths as $path)
            {
                self::$_instance->addAutoloadDirectory($path);
            }
        }

        return self::$_instance;
    }


    /** @var string */
    private $_root;


    /**
     * Constructor. Should not be called directly.
     *
     * @see Tempest::instantiate() To create a new instance instead.
     *
     * @param string $root The application root directory.
     */
    public function __construct($root)
    {
        $this->_root = $root;
    }


    public function __get($prop)
    {
        if ($prop === 'root') return $this->_root;

        return null;
    }


    /**
     * Register an autoloader to run in a given directory.
     *
     * @param string $path The directory to add.
     *
     * @throws Exception
     */
    public function addAutoloadDirectory($path)
    {
        $this->attempt(function() use ($path) {
            $path = ROOT . '/' . trim($path, '/') . '/';

            if (is_dir($path))
            {
                spl_autoload_register(function($class) use ($path) {
                    $file = str_replace('\\', '/', $class) . '.php';

                    if (is_file($file))
                    {
                        require_once $file;

                        if (!class_exists($class))
                        {
                            throw new Exception('Could not find class ' . $class . '.');
                        }
                    }
                    else
                    {
                        throw new Exception('Could not load file ' . $file . '.');
                    }
                });
            }
            else
            {
                throw new Exception('Directory ' . $path . ' does not exist.');
            }
        });
    }


    /**
     * Attempt to execute a block of code. If any exceptions are thrown in the attempted block, they will be caught and
     * displayed in Tempest's exception page.
     *
     * @param callable $callable Block of code to attempt to execute.
     */
    public function attempt($callable)
    {
        try
        {
            $callable();
        }

        catch (Exception $exception)
        {
            header('Content-Type: text/plain');

            // TODO: Nice exception display.
            // See vendor/martywallace/tempest/templates.

            $stack = $exception->getTrace();
            var_dump($stack);

            exit;
        }
    }


    /**
     * Start running the application.
     */
    public function start()
    {
        $this->attempt(function() {
            $this->setup();
        });
    }


    /**
     * Set up the application.
     *
     * @throws Exception
     */
    protected abstract function setup();

}