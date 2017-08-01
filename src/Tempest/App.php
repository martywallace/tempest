<?php namespace Tempest;

/**
 * The core application class, from which your own core application class extends. The App class is responsible for
 * bootstrapping your services and configuration.
 *
 * @property-read string $root The application root directory - the result of moving on directory up from the value
 * provided to {@link App::make()}. Always without a trailing slash.
 *
 * @package Tempest
 * @author Marty Wallace
 */
class App {

	/**
	 * Create an application instance.
	 *
	 * @param string $root The value of __DIR__ within the application's public root directory.
	 *
	 * @return static
	 */
	public static function make($root) {
		return new static($root);
	}

	/** @var string */
	private $_root;

	/**
	 * @internal
	 * @see static::make()
	 */
	public function __construct($root) {
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