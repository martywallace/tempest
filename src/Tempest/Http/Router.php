<?php namespace Tempest\Http;

use Tempest\Tempest;
use FastRoute\Dispatcher;
use FastRoute\RouteCollector;
use Tempest\Utils\ArrayUtil;

/**
 * The application router.
 *
 * @property-read Request $request The request made to the application.
 * @property-read Response $response The response to be sent to the client.
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

	/** @var bool */
	private $_dispatched = false;

	public function __construct() {
		$this->_request = new Request();
		$this->_response = new Response();
		$this->_routes = new RouteGroup();
	}

	public function __get($prop) {
		if ($prop === 'request') return $this->_request;
		if ($prop === 'response') return $this->_response;

		return null;
	}

	public function __isset($prop) {
		return property_exists($this, $prop) || $this->{$prop} !== null;
	}

	/**
	 * Adds middleware to the beginning of every request.
	 *
	 * @param Action|Action[] $action The middleware action.
	 */
	public function middleware($action) {
		$this->_middleware = array_merge($this->_middleware, ArrayUtil::forceArray($action));
	}

	/**
	 * Adds routes to listen for in the application.
	 *
	 * @param RouteLike[] $routes One or more {@link Route routes} or {@link RouteGroup route groups}.
	 */
	public function routes(array $routes) {
		$this->_routes->add($routes);
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
	 * @param string $uri
	 * @param RouteLike[] $grouped
	 *
	 * @return RouteGroup
	 */
	public function group($uri, array $grouped) {
		return new RouteGroup($uri, $grouped);
	}

	/** @see route() */
	public function get($uri, Action $action) { return $this->route('GET', $uri, $action); }

	/** @see route() */
	public function post($uri, Action $action) { return $this->route('POST', $uri, $action); }

	/** @see route() */
	public function put($uri, Action $action) { return $this->route('PUT', $uri, $action); }

	/** @see route() */
	public function patch($uri, Action $action) { return $this->route('PATCH', $uri, $action); }

	/** @see route() */
	public function delete($uri, Action $action) { return $this->route('DELETE', $uri, $action); }

	/** @see route() */
	public function head($uri, Action $action) { return $this->route('HEAD', $uri, $action); }

	/**
	 * @internal
	 */
	public function dispatch() {
		if (!$this->_dispatched) {
			$dispatcher = \FastRoute\simpleDispatcher(function (RouteCollector $collector) {
				foreach ($this->_routes->flatten() as $route) {
					$collector->addRoute($route->method, $route->uri, $route);
				}
			});

			$info = $dispatcher->dispatch($this->_request->method, $this->_request->uri);

			if ($info[0] === Dispatcher::FOUND) {
				// $info[2] contains named data.
				// Successful route match.
				$this->_request->attachNamed($info[2]);

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

		$this->_dispatched = true;
	}

}