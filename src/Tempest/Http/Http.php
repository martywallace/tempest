<?php namespace Tempest\Http;

use Exception;
use Tempest\{App, Kernel};

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
				$external = require App::get()->root . DIRECTORY_SEPARATOR . $routes;

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
	 * Create a new {@link Route route}.
	 *
	 * @param string|string[] $method The HTTP method(s) to associate this route with.
	 * @param string $uri The URI that will trigger this route.
	 *
	 * @return Route
	 */
	public function route($method, $uri) {
		return new Route(strtoupper($method), $uri);
	}

	/**
	 * Create a new {@link Route route} with its method set to GET.
	 *
	 * @param string $uri The URI that will trigger this route.
	 *
	 * @return Route
	 */
	public function get($uri) {
		return $this->route('GET', $uri);
	}

	/**
	 * Create a new {@link Route route} with its method set to POST.
	 *
	 * @param string $uri The URI that will trigger this route.
	 *
	 * @return Route
	 */
	public function post($uri) {
		return $this->route('POST', $uri);
	}

	/**
	 * Create a new {@link Route route} with its method set to PUT.
	 *
	 * @param string $uri The URI that will trigger this route.
	 *
	 * @return Route
	 */
	public function put($uri) {
		return $this->route('PUT', $uri);
	}

	/**
	 * Create a new {@link Route route} with its method set to PATCH.
	 *
	 * @param string $uri The URI that will trigger this route.
	 *
	 * @return Route
	 */
	public function patch($uri) {
		return $this->route('PATCH', $uri);
	}

	/**
	 * Create a new {@link Route route} with its method set to DELETE.
	 *
	 * @param string $uri The URI that will trigger this route.
	 *
	 * @return Route
	 */
	public function delete($uri) {
		return $this->route('DELETE', $uri);
	}

	/**
	 * Create a new {@link Route route} with its method set to HEAD.
	 *
	 * @param string $uri The URI that will trigger this route.
	 *
	 * @return Route
	 */
	public function head($uri) {
		return $this->route('HEAD', $uri);
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