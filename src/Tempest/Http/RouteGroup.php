<?php namespace Tempest\Http;

use Tempest\Utils\ArrayUtil;

/**
 * A group of {@link Route routes} and {@link RouteGroup route groups}.
 *
 * @package Tempest\Http
 * @author Marty Wallace
 */
class RouteGroup extends RouteLike {

	/** @var RouteLike[]|Route[]|RouteGroup[] */
	private $_children = [];

	public function __construct($uri = '/', array $children = []) {
		foreach ($children as $child) $this->add($child);
		parent::__construct($uri);
	}

	/**
	 * Adds a {@link Route route} or {@link RouteGroup route group} to this group.
	 *
	 * @param RouteLike|RouteLike[] $routeLike
	 *
	 * @return $this
	 */
	public function add($routeLike) {
		$this->_children = array_merge($this->_children, ArrayUtil::forceArray($routeLike));
		return $this;
	}

	/**
	 * Returns a flattened group of routes by recursively combining all descendant routes.
	 *
	 * @param RouteGroup $parent A parent group triggering this flatten.
	 *
	 * @return Route[]
	 */
	public function flatten($parent = null) {
		$routes = [];

		if (!empty($parent)) $this->setParent($parent);

		foreach ($this->_children as $child) {
			if ($child instanceof Route) $routes[] = $child->setParent($this);
			else if ($child instanceof RouteGroup) $routes = array_merge($routes, $child->flatten($this));
		}

		return $routes;
	}

}