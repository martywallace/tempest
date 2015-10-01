<?php namespace Tempest\Http;

/**
 * A controller manages a collection of routes and the appropriate reaction to those routes being triggered.
 *
 * @package Tempest\Http
 * @author Marty Wallace
 */
abstract class Controller {

	public function __get($prop) {
		return null;
	}

	public abstract function bindRoutes();

}