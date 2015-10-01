<?php namespace Tempest\Http;

/**
 * A request made to the application.
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

	/**
	 * Returns request data e.g. GET or POST data.
	 *
	 * @param string $name The name of the data to get.
	 * @param mixed $fallback A fallback value to use if the data is not defined.
	 *
	 * @return mixed
	 */
	public function data($name = null, $fallback = null) {
		$stack = [];

		if (app()->router->method === 'GET') $stack = $_GET;
		if (app()->router->method === 'POST') $stack = $_POST;

		if ($name === null) {
			return $stack;
		}

		return array_key_exists($name, $stack) ? $stack[$name] : $fallback;
	}

	/**
	 * Return data provided in the request URI against dynamic components.
	 *
	 * @param string $name The argument name provided in the route definition.
	 * @param mixed $fallback A fallback value to use if the argument was not provided.
	 *
	 * @return mixed
	 */
	public function arg($name = null, $fallback = null) {
		return array_key_exists($name, $this->_args) ? $this->_args[$name] : $fallback;
	}

}