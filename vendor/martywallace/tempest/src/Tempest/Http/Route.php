<?php namespace Tempest\Http;

/**
 * A single route definition.
 *
 * @property-read string $route The route pattern.
 * @property-read string $method The request method used to access this route.
 * @property-read callable $handler The route handler.
 * @property-read callable[] $middleware A list of the middleware attached to this route.
 *
 * @package Tempest\Http
 */
class Route {

	/** @var string */
	private $_route;

	/** @var string */
	private $_method;

	/** @var callable */
	private $_handler;

	/** @var callable[] */
	private $_middleware;

	public function __construct($route, Array $detail) {
		$this->_route = $route;
		$this->_method = count($detail) >= 1 ? strtoupper($detail[0]) : null;
		$this->_handler = count($detail) >= 2 ? $detail[1] : null;

		if (count($detail) >= 3) {
			if (!is_array($detail[2]) || is_callable($detail[2])) {
				// Convert to array of callables.
				$detail[2] = array($detail[2]);
			}

			$this->_middleware = $detail[2];
		} else {
			$this->_middleware = null;
		}
	}

	public function __get($prop) {
		if ($prop === 'route') return $this->_route;
		if ($prop === 'method') return $this->_method;
		if ($prop === 'handler') return $this->_handler;
		if ($prop === 'middleware') return $this->_middleware;

		return null;
	}

}