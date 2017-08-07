<?php namespace Tempest\Http;

use Exception;
use Tempest\Kernel;

/**
 * The HTTP kernel deals with interpreting a HTTP request and generating a {@link Response response}.
 *
 * @author Marty Wallace
 */
class Http extends Kernel {

	/** @var Route[] */
	private $_routes = [];

	/**
	 * Handle an incoming {@link Request HTTP request} and generate a {@link Response response} for sending.
	 *
	 * @param Request $request The request to handle.
	 * @param callable|string $routes Known routes to match the request against. Can either be a function accepting this
	 * HTTP instance or a string pointing to a PHP file that returns a function accepting this HTTP instance.
	 *
	 * @return Response
	 *
	 * @throws Exception
	 */
	public function handle(Request $request, $routes = null) {
		if (!empty($routes)) {
			if (is_callable($routes)) {
				// Function provided directly.
				$this->_routes = $routes($this);
			} else {
				$external = require $this->app->root . DIRECTORY_SEPARATOR . $routes;

				if (!is_callable($external)) {
					throw new Exception('External route files must return a callable that returns an array of routes to handle.');
				}

				$this->_routes = $external($this);
			}

			$this->_routes = $this->_flatten($this->_routes);
		}

		return new Response();
	}

	/**
	 * Flatten a tree of routes.
	 *
	 * @param Route[] $routes The routes to flatten.
	 *
	 * @return Route[]
	 */
	private function _flatten(array $routes) {
		return $routes;
	}

}