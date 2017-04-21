<?php namespace Tempest\Http;

use Tempest\Utils\ArrayUtil;

/**
 * An instance representative of a route or group of routes.
 *
 * @property-read string $uri The URI associated with this instance.
 *
 * @package Tempest\Http
 * @author Marty Wallace
 */
abstract class RouteLike {

	/** @var string */
	private $_uri;

	/** @var Action[] */
	private $_middleware = [];

	public function __construct($uri = '/') {
		$this->_uri = $uri;
	}

	public function __get($prop) {
		if ($prop === 'uri') return $this->_uri;

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

	/**
	 * Set a parent for this route, prefixing its URI with that of its parent.
	 *
	 * @param RouteGroup $parent The target parent.
	 *
	 * @return $this
	 */
	public function setParent(RouteGroup $parent) {
		$parts = preg_split('/\/+/', trim($parent->uri, '/'));
		$parts[] = trim($this->_uri, '/');

		$this->_uri = '/' . implode('/', array_filter($parts, function($part) { return !empty($part); }));

		return $this;
	}

}