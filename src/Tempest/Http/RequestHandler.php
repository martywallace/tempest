<?php namespace Tempest\Http;

use Tempest\Tempest;

/**
 * An instance that appears along the chain between a request and the final response, usually Middleware or a Controller.
 *
 * @property-read Request $request The request made to the application.
 * @property-read Response $response The response to be sent by the application.
 *
 * @package Tempest\Http
 * @author Marty Wallace
 */
class RequestHandler {

	public function __get($prop) {
		if ($prop === 'request') return Tempest::get()->router->request;
		if ($prop === 'response') return Tempest::get()->router->response;

		return null;
	}

}