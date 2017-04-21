<?php namespace Tempest\Http;

use Tempest\Utils\ArrayUtil;


/**
 * A single route definition.
 *
 * @property-read string $method The request method.
 * @property-read string $uri The request URI.
 * @property-read Action $action The controller action.
 *
 * @package Tempest\Http
 * @author Marty Wallace
 */
class Route {

	/** @var string */
	private $_method;

	/** @var string */
	private $_uri;

	/** @var string */
	private $_action;

	/** @var Action[] */
	private $_middleware = [];

	public function __construct($method, $uri, $action) {
		$this->_method = $method;
		$this->_uri = $uri;
		$this->_action = $action;
	}

	public function __get($prop) {
		if ($prop === 'method') return $this->_method;
		if ($prop === 'uri') return $this->_uri;
		if ($prop === 'action') return $this->_action;

		return null;
	}

	/**
	 * Attach middleware to this route.
	 *
	 * @param Action|Action[] $middleware One or more middleware actions.
	 *
	 * @return $this
	 */
	public function middleware($middleware) {
		$this->_middleware = array_merge($this->_middleware, ArrayUtil::forceArray($middleware));

		return $this;
	}

	/**
	 * Get all attached middleware.
	 *
	 * @return Action[]
	 */
	public function getMiddleware() {
		return $this->_middleware;
	}

	/**
	 * Determine whether this route uses middleware.
	 *
	 * @return bool
	 */
	public function hasMiddleware() {
		return count($this->_middleware) > 0;
	}

}