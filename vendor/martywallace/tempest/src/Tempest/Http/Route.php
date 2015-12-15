<?php namespace Tempest\Http;

use Exception;


/**
 * A single route definition.
 *
 * @property-read string $route The route pattern.
 * @property-read string $method The request method used to access this route.
 * @property-read string $handler The route handler.
 * @property-read string[] $middleware Middleware to trigger before the handler is reached.
 *
 * @package Tempest\Http
 */
class Route {

	/** @var string */
	private $_route;

	/** @var string */
	private $_method;

	/** @var string */
	private $_handler;

	/** @var string[] */
	private $_middleware = array();

	public function __construct($route, $detail) {
		$this->_route = $route;
		$this->_method = 'GET';

		if (!empty($detail) && (is_array($detail) || is_string($detail))) {
			if (is_array($detail)) {
				if (count($detail) === 1) {
					// Only a handler, default to GET (above).
					$this->_handler = $detail[0];
				}

				if (count($detail) >= 2) {
					// A handler and the request method.
					$this->_method = strtoupper($detail[0]);
					$this->_handler = $detail[1];

					if (count($detail) >= 3) {
						// Includes middleware.
						$this->_middleware = is_array($detail[2]) ? $detail[2] : array($detail[2]);
					}
				}
			} else {
				$this->_handler = $detail;
			}
		} else {
			throw new Exception('Invalid route definition for "' . $route . '".');
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