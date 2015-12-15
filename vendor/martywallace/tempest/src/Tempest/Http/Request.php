<?php namespace Tempest\Http;

/**
 * A request made to the application.
 *
 * @property-read string $method The request method e.g. GET, POST.
 *
 * @package Tempest\Http
 * @author Marty Wallace
 */
class Request {

	/** @var array */
	private $_args;

	public function __construct(Array $args) {
		$this->_args = $args;
	}

	public function __get($prop) {
		if ($prop === 'method') return app()->router->method;

		return null;
	}

	/**
	 * Returns request data e.g. GET or POST data, based on the request method.
	 *
	 * @param string $name The name of the data to get.
	 * @param mixed $fallback A fallback value to use if the data does not exist.
	 *
	 * @return mixed
	 */
	public function data($name = null, $fallback = null) {
		$stack = array();

		if (app()->router->method === 'GET') $stack = $_GET;
		if (app()->router->method === 'POST') $stack = $_POST;

		if ($name === null) {
			return $stack;
		}

		return array_key_exists($name, $stack) ? $stack[$name] : $fallback;
	}

	/**
	 * Return data provided in the request URI against dynamic named components.
	 *
	 * @param string $name The argument name provided in the route definition.
	 * @param mixed $fallback A fallback value to use if the argument was not provided.
	 *
	 * @return mixed
	 */
	public function named($name = null, $fallback = null) {
		return $name === null ? $this->_args
			: (array_key_exists($name, $this->_args) ? $this->_args[$name] : $fallback);
	}

}