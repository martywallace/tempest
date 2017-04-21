<?php namespace Tempest\Http;

use Tempest\Utils\ArrayUtil;


/**
 * A single route definition.
 *
 * @property-read string $method The request method.
 * @property-read Action $action The controller action.
 *
 * @package Tempest\Http
 * @author Marty Wallace
 */
class Route extends RouteLike {

	/** @var string */
	private $_method;

	/** @var string */
	private $_action;

	public function __construct($method, $uri, $action) {
		$this->_method = $method;
		$this->_action = $action;

		parent::__construct($uri);
	}

	public function __get($prop) {
		if ($prop === 'method') return $this->_method;
		if ($prop === 'action') return $this->_action;

		return parent::__get($prop);
	}

}