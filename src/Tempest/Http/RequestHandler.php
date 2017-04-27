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
	 * @param array $meta Meta information to attach to the action.
	 *
	 * @return Action
	 */
	public static function action($name, array $meta = []) {
		return new Action(static::class, $name, $meta);
	}

}