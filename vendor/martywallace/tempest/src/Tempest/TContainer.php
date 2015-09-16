<?php namespace Tempest;

use Slim\Container;

/**
 * TContainer wraps a Slim Container, setting Tempest level defaults for its content.
 *
 * @property-read Container $container The internal Slim Container.
 *
 * @package Tempest
 * @author Marty Wallace
 */
class TContainer {

	/** @var Container */
	private $_container;

	public function __construct() {
		$this->_container = new Container();
	}

	public function __get($prop) {
		if ($prop === 'container') return $this->_container;

		return null;
	}

}