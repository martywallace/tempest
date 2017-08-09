<?php namespace Tempest\Http;

use Exception;
use Tempest\{App, Kernel};
use FastRoute\{RouteCollector, Dispatcher};

/**
 * The HTTP kernel deals with interpreting a HTTP request and generating a {@link Response response}.
 *
 * @author Marty Wallace
 */
class Http extends Kernel {

	/** @var Group */
	private $_routes;

	protected function __construct() {
		$this->_routes = new Group();
	}

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
		// First determine what routes are defined (if any) and define them.
		if (!empty($routes)) {
			if (is_callable($routes)) {
				// Function provided directly.
				$this->_routes->add($routes($this));
			} else {
				$external = require App::get()->root . DIRECTORY_SEPARATOR . $routes;

				if (!is_callable($external)) {
					throw new Exception('External route files must return a callable that returns an array of routes to handle.');
				}

				$this->_routes->add($external($this));
			}
		}

		// Attempt to match a route.
		$info = \FastRoute\simpleDispatcher(function (RouteCollector $collector) {
			foreach ($this->_routes->flatten() as $route) {
				$collector->addRoute($route->method, $route->uri, $route);
			}
		})->dispatch($request->method, $request->uri);

		if ($info[0] === Dispatcher::FOUND) return $this->found($request, $info[1], $info[2]);
		else if ($info[0] === Dispatcher::NOT_FOUND) return $this->notFound($request);
		else if ($info[0] === Dispatcher::METHOD_NOT_ALLOWED) return $this->methodNotAllowed($request, $info[1]);

		return null;
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
	 * Handle a successfully matched route.
	 *
	 * @param Request $request The request being handled.
	 * @param Route $route The route that was matched.
	 * @param mixed[] $named Named arguments provided in the request, defined by the route.
	 *
	 * @return Response
	 */
	protected function found(Request $request, Route $route, array $named) {
		return new Response();
	}

	/**
	 * Handle no route match.
	 *
	 * @param Request $request The request being handled.
	 *
	 * @return Response
	 */
	protected function notFound(Request $request) {
		return new Response();
	}

	/**
	 * Handle a matched route with an unsupported method.
	 *
	 * @param Request $request The request being handled.
	 * @param string[] $allowed The allowed methods.
	 *
	 * @return Response
	 */
	protected function methodNotAllowed(Request $request, array $allowed) {
		return new Response();
	}

}