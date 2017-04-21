<?php namespace Tempest\Http;

/**
 * An instance that appears along the chain between a request and the final response, usually Middleware or a Controller.
 *
 * @package Tempest\Http
 * @author Marty Wallace
 */
class RequestHandler {

	/**
	 * Get an action statically.
	 *
	 * @param string $name The name of the action.
	 *
	 * @return Action
	 */
	public static function action($name) {
		return new Action(static::class, $name);
	}

}