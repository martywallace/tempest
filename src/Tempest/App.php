<?php namespace Tempest;

use Exception;

/**
 * The core application class, from which your own core application class extends. The App class is responsible for
 * bootstrapping your services and configuration.
 *
 * @property-read string $root The application root directory - the result of moving on directory up from the value
 * provided to {@link App::boot()}. Always without a trailing slash.
 *
 * @author Marty Wallace
 */
abstract class App {

	/** @var static */
	protected static $_instance;

	/**
	 * Create an application instance.
	 *
	 * @param string $root The application root directory, usually one level above the webroot.
	 * @param array $config Application configuration.
	 *
	 * @return static
	 *
	 * @throws Exception If the application has already been booted.
	 */
	public static function boot($root, array $config = []) {
		if (!empty(static::$_instance)) {
			throw new Exception('The application has already been booted.');
		}

		static::$_instance = new static($root, $config);
		return static::$_instance;
	}

	/**
	 * Statically get the application instance. {@link App::boot()} must be called before this.
	 *
	 * @return App
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

	/**
	 * @see static::boot()
	 *
	 * @param string $root
	 * @param array $config
	 */
	private function __construct($root, array $config) {
		$this->_root = rtrim($root, '/');
		$this->_config = $config;

		array_walk_recursive($config, function($value, $key) {
			if (strpos($key, '.') !== false) {
				throw new Exception('Configuration fields cannot contain the "." character, as this is used for nested property querying.');
			}
		});
	}

	public function __get($prop) {
		if ($prop === 'root') return $this->_root;

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
	public function config($query, $fallback = null) {
		return Utility::dig($this->_config, $query, $fallback);
	}

	/**
	 * Run the application and provide output to the parent process.
	 *
	 * @param null $actor
	 *
	 * @return string
	 */
	public function run($actor = null) {
		return 'Hello world!';
	}

}