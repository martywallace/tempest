<?php namespace Tempest;

use Exception;

/**
 * The core application class, from which your own core application class extends. The App class is responsible for
 * bootstrapping your services and configuration.
 *
 * @property-read string $root The application root directory - the result of moving on directory up from the value
 * provided to {@link App::boot()}. Always without a trailing slash.
 *
 * @package Tempest
 * @author Marty Wallace
 */
class App {

	/** @var static */
	protected static $_instance;

	/**
	 * Create an application instance.
	 *
	 * @param string $root The value of __DIR__ within the application's public root directory.
	 *
	 * @return static
	 *
	 * @throws Exception If the application has already been booted.
	 */
	public static function boot($root) {
		if (!empty(static::$_instance)) {
			throw new Exception('The application has already been booted.');
		}

		static::$_instance = new static($root);
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

	/**
	 * @see static::boot()
	 *
	 * @param string $root
	 */
	private function __construct($root) {
		$this->_root = rtrim(realpath($root . '/../'), '/');
	}

	public function __get($prop) {
		if ($prop === 'root') return $this->_root;

		return null;
	}

	public function __isset($name) {
		return !empty($this->{$name});
	}

}