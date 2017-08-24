<?php namespace Tempest\Http;

/**
 * A group of routes.
 *
 * @author Marty Wallace
 */
class Group extends Resource {

	/** @var Route[]|Group[] */
	private $_children = [];

	/**
	 * Group constructor.
	 *
	 * @param string $uri
	 * @param Route[]|Group[] $children
	 */
	public function __construct($uri = '/', array $children = []) {
		parent::__construct($uri);
		$this->_children = $children;
	}

	/**
	 * Get all child routes and groups.
	 *
	 * @return Group[]|Route[]
	 */
	public function getChildren() {
		return $this->_children;
	}

	/**
	 * Adds one or more {@link Route routes} or {@link Group route groups} to this group.
	 *
	 * @param Route|Group|Route[]|Group[] $children The child or children to add.
	 */
	public function add($children) {
		if (!is_array($children)) $children = [$children];

		$this->_children = array_merge($this->_children, $children);
	}

	/**
	 * Flatten this group into one level of {@link Route routes}, by recursively merging the URI of all descendants.
	 *
	 * @return Route[]
	 */
	public function flatten() {
		$routes = [];

		foreach ($this->_children as $child) {
			$child->prependUri($this->getUri());

			foreach ($this->getMiddleware() as $middleware) {
				$child->prependMiddleware($middleware[0], $middleware[1]);
			}

			if ($child instanceof Group) {
				$routes = array_merge($routes, $child->flatten());
			} else {
				$routes[] = $child;
			}
		}

		return $routes;
	}

}