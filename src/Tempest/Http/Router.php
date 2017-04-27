<?php namespace Tempest\Http;

use Tempest\Tempest;
use FastRoute\{Dispatcher, RouteCollector};

/**
 * The application router.
 *
 * @package Tempest\Http
 * @author Marty Wallace
 */
final class Router {

	/** @var Request */
	private $_request;
	
	/** @var Response */
	private $_response;

	/** @var Action[] */
	private $_middleware = [];

	/** @var RouteGroup */
	private $_routes;

	public function __construct(Request $request, Response $response) {
		$this->_request = $request;
		$this->_response = $response;
		$this->_routes = new RouteGroup();
	}

	public function __isset($prop) {
		return property_exists($this, $prop) || $this->{$prop} !== null;
	}

	/**
	 * Adds a group of routes to listen for in the application.
	 *
	 * @param callable $provider A function used to provide the routes. The function will be provided the {@link Router}
	 * instance as the first argument.
	 *
	 * @return RouteGroup
	 */
	public function add(callable $provider) {
		return $this->_routes->add($provider($this));
	}

	/**
	 * Use a provided middleware action for every request. Middleware defined here will be executed before any
	 * middleware attached to routes or route groups.
	 *
	 * @param Action[] ...$middleware The middleware actions to use.
	 */
	public function middleware(Action ...$middleware) {
		$this->_middleware = array_merge($this->_middleware, $middleware);
	}

	/**
	 * Adds a route to handle.
	 *
	 * @param string $method The HTTP method that this route handles.
	 * @param string $uri The request URI that this route handles.
	 * @param Action $action The controller action that this route triggers when matched.
	 *
	 * @return Route
	 */
	public function route($method, $uri, Action $action) {
		return new Route($method, $uri, $action);
	}

	/**
	 * Adds a group of routes to handle, using a prefix URI.
	 *
	 * @param string $uri The base URI for the group, prepended to every descendant route or group.
	 * @param RouteLike[] $grouped The list of routes and groups within this group.
	 *
	 * @return RouteGroup
	 */
	public function group($uri, array $grouped) {
		return new RouteGroup($uri, $grouped);
	}

	/**
	 * Adds a GET route to handle.
	 *
	 * @param string $uri The request URI that this route handles.
	 * @param Action $action The controller action that this route triggers when matched.
	 *
	 * @return Route
	 */
	public function get($uri, Action $action) { return $this->route('GET', $uri, $action); }

	/**
	 * Adds a POST route to handle.
	 *
	 * @param string $uri The request URI that this route handles.
	 * @param Action $action The controller action that this route triggers when matched.
	 *
	 * @return Route
	 */
	public function post($uri, Action $action) { return $this->route('POST', $uri, $action); }

	/**
	 * Adds a PUT route to handle.
	 *
	 * @param string $uri The request URI that this route handles.
	 * @param Action $action The controller action that this route triggers when matched.
	 *
	 * @return Route
	 */
	public function put($uri, Action $action) { return $this->route('PUT', $uri, $action); }

	/**
	 * Adds a PATCH route to handle.
	 *
	 * @param string $uri The request URI that this route handles.
	 * @param Action $action The controller action that this route triggers when matched.
	 *
	 * @return Route
	 */
	public function patch($uri, Action $action) { return $this->route('PATCH', $uri, $action); }

	/**
	 * Adds a DELETE route to handle.
	 *
	 * @param string $uri The request URI that this route handles.
	 * @param Action $action The controller action that this route triggers when matched.
	 *
	 * @return Route
	 */
	public function delete($uri, Action $action) { return $this->route('DELETE', $uri, $action); }

	/**
	 * Adds a HEAD route to handle.
	 *
	 * @param string $uri The request URI that this route handles.
	 * @param Action $action The controller action that this route triggers when matched.
	 *
	 * @return Route
	 */
	public function head($uri, Action $action) { return $this->route('HEAD', $uri, $action); }

	/**
	 * @internal
	 */
	public function dispatch() {
		$dispatcher = \FastRoute\simpleDispatcher(function (RouteCollector $collector) {
			foreach ($this->_routes->flatten() as $route) {
				$collector->addRoute($route->method, $route->uri, $route);
			}
		});

		$info = $dispatcher->dispatch($this->_request->method, $this->_request->uri);

		if ($info[0] === Dispatcher::FOUND) {
			// $info[2] contains named data.
			// Successful route match.
			foreach ($info[2] as $named => $value) $this->_request->setNamed($named, $value);

			/** @var Route $route */
			$route = $info[1];
			$output = null;

			/** @var Action[] $actions */
			$actions = array_merge($this->_middleware, $route->getMiddleware(), [$route->action]);

			for ($i = 0; $i < count($actions); $i++) {
				if ($i < count($actions) - 1) $actions[$i]->bind($this->_request, $this->_response, [$actions[$i + 1], 'execute']);
				else $actions[$i]->bind($this->_request, $this->_response);
			}

			$output = $actions[0]->execute();

			if ($output !== null && $output !== false) {
				// If the controller returns a non-null or non-false value, overwrite the response body with that value.
				$this->_response->body = $output;
			}
		}

		if ($info[0] === Dispatcher::NOT_FOUND) {
			$useTemplate = false;

			if (!empty($this->_request->uri)) {
				// Attempt to load HTML file with the same name.
				// Hitting the root looks for index.html.
				$template = ($this->_request->uri === '/' ? 'index' : $this->_request->uri) . '.html';

				if ($this->_request->method === 'GET' && Tempest::get()->twig->loader->exists($template)) {
					$useTemplate = true;

					foreach (explode('/', $template) as $part) {
						// Don't use templates if it or any ancestor directory begins with an underscore.
						if (strpos($part, '_') === 0) {
							$useTemplate = false;
							break;
						}
					}

					if ($useTemplate) $this->_response->body = Tempest::get()->twig->render($template);
				}
			}

			if (!$useTemplate) {
				$this->_response->status = Status::NOT_FOUND;
			}
		}

		if ($info[0] === Dispatcher::METHOD_NOT_ALLOWED) {
			$this->_response->status = Status::METHOD_NOT_ALLOWED;
		}

		$this->_response->send();
	}

	/**
	 * Render documentation for the routes defined.
	 */
	public function document() {
		$docs = array_map(function(Route $route) {
			return [
				'method' => $route->method,
				'uri' => $route->uri,
				'controller' => $route->action->class . '::' . $route->action->method,
				'middleware' => array_map(function(Action $action) {
					return $action->class . '::' . $action->method;
				}, $route->getMiddleware())
			];
		}, $this->_routes->flatten());

		Tempest::get()->dump($docs);
	}

}