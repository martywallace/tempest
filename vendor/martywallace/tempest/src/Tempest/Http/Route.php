<?php namespace Tempest\Http;

use Exception;


/**
 * A single route definition.
 *
 * @property-read string $route The route pattern.
 * @property-read string $method The request method used to access this route.
 * @property-read string $handler The route handler.
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

	public function __construct($route, $detail) {
		$this->_route = $route;
		$this->_method = 'GET';

		if (!empty($detail) && (is_array($detail) || is_string($detail))) {
			if (is_array($detail)) {
				if (count($detail) > 1) {
					$this->_method = strtoupper($detail[0]);
					$this->_handler = $detail[1];
				} else {
					$this->_handler = $detail[0];
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

		return null;
	}

}