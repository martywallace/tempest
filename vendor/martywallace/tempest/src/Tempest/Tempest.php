<?php

namespace Tempest;

use Exception;


/**
 * Tempest's core.
 *
 * @property-read string $root The framework root directory.
 *
 * @author Marty Wallace.
 */
class Tempest
{

    /**
     * @var Tempest
     */
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


    /**
     * @var string
     */
    private $_root;


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
     */
    public function addAutoloadDirectory($path)
    {
        $path = trim($path, '/');
        spl_autoload_register(function($class) use ($path)
        {
            $file = ROOT . '/' . $path . '/' . str_replace('\\', '/', $class) . '.php';

            if (is_file($file))
            {
                require_once $file;

                if (!class_exists($class))
                {
                    throw new Exception('Could not find class ' . $class);
                }
            }
            else
            {
                throw new Exception('Could not load file ' . $file);
            }
        });
    }


    /**
     * Start running the application.
     */
    public function start()
    {
        try
        {
            $this->setup();
        }
        catch(Exception $e)
        {
            // TODO: Nice exception output.
            $stack = $e->getTrace();
            var_dump($stack);
        }
    }


    protected function setup()
    {
        //
    }

}