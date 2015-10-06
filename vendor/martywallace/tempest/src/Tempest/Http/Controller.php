<?php namespace Tempest\Http;

/**
 * A controller manages a collection of routes and the appropriate reaction to those routes being triggered.
 *
 * @package Tempest\Http
 * @author Marty Wallace
 */
abstract class Controller {

	/**
	 * Binds routes to this controller.
	 *
	 * @return array[]
	 */
	public abstract function bindRoutes();

}