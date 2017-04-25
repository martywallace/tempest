<?php namespace Tempest\Http;

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

	/** @var RouteGroup */
	private $_parent;

	/** @var Action[] */
	private $_middleware = [];

	public function __construct($uri = '/') {
		$this->_uri = $uri;
	}

	public function __get($prop) {
		if ($prop === 'uri') {
			if (!empty($this->_parent)) {
				return '/' . trim($this->_parent->uri . $this->_uri, '/');
			} else {
				return $this->_uri;
			}
		}

		return null;
	}

	/**
	 * Attach middleware to this route.
	 *
	 * @param Action[] ...$middleware One or more middleware actions.
	 *
	 * @return $this
	 */
	public function middleware(...$middleware) {
		$this->_middleware = array_merge($this->_middleware, $middleware);

		return $this;
	}

	/**
	 * Get all attached middleware.
	 *
	 * @return Action[]
	 */
	public function getMiddleware() {
		return array_merge(!empty($this->_parent) ? $this->_parent->getMiddleware() : [], $this->_middleware);
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
	 */
	protected function setParent(RouteGroup $parent) {
		$this->_parent = $parent;
	}

	/**
	 * @return RouteGroup
	 */
	public function getParent() {
		return $this->_parent;
	}

	/**
	 * @return RouteGroup[]
	 */
	public function getAncestors() {
		if (!empty($this->_parent)) {
			$next = $this->_parent;
			$ancestors = [$this->_parent];

			while ($next->_parent) {
				$next = $next->_parent;
				$ancestors[] = $next;
			}

			return $ancestors;
		}

		return [];
	}

}