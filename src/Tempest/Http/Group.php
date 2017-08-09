<?php namespace Tempest\Http;

/**
 * A group of routes.
 *
 * @property-read string $uri The URI from which all descendant routes are made relative to.
 * @property-read Route[]|Group[] $children The children routes and groups.
 *
 * @author Marty Wallace
 */
class Group {

	/** @var Route[]|Group[] */
	private $_children = [];

	/** @var string */
	private $_uri;

	/**
	 * Group constructor.
	 *
	 * @param string $uri
	 */
	public function __construct($uri = '/') {
		$this->_uri = $uri;
	}

	public function __get($prop) {
		if ($prop === 'uri') return $this->_uri;
		if ($prop === 'children') return $this->_children;

		return null;
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
		return $this->_children; // TODO
	}

}