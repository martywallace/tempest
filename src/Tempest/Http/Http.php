<?php namespace Tempest\Http;

use Exception;
use Tempest\{App, Events\ExceptionEvent, Kernel};
use FastRoute\{RouteCollector, Dispatcher};

/**
 * The HTTP kernel deals with interpreting a HTTP {@link Request request} and generating a {@link Response response}.
 *
 * @author Marty Wallace
 */
class Http extends Kernel {

	/** @var Group */
	private $_routes;

	public function __construct() {
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
		try {
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
		} catch (Exception $exception) {
			$this->dispatch(ExceptionEvent::EXCEPTION, new ExceptionEvent($exception));
			return $this->exception($exception);
		}
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
	 *
	 * @throws Exception If the matched route does not perform any valid action.
	 */
	protected function found(Request $request, Route $route, array $named) {
		foreach ($named as $property => $value) {
			// Attached all named route data.
			$request->attachNamed($property, $value);
		}

		if ($route->getMode() === Route::MODE_UNDETERMINED) {
			throw new Exception('Route "' . $route->uri . '" does not perform a valid action');
		}

		if ($route->getMode() === Route::MODE_TEMPLATE) {
			return Response::make()
				->body(App::get()->twig->render($route->getTemplate()));
		}

		if ($route->getMode() === Route::MODE_CONTROLLER) {
			$controller = $route->getController();

			if (!class_exists($controller[0])) {
				throw new Exception('Controller class "' . $controller[0] . '" does not exist.');
			}

			$instance = new $controller[0]();

			if (!method_exists($instance, $controller[1])) {
				throw new Exception('Controller "' . $controller[0] . '" does not define a method "' . $controller[1] . '".');
			}

			$response = Response::make();
			$body = $instance->{$controller[1]}($request, $response);

			if ($body !== null) $response->body($body);

			return $response;
		}

		return Response::make();
	}

	/**
	 * Handle no route match.
	 *
	 * @param Request $request The request being handled.
	 *
	 * @return Response
	 */
	protected function notFound(Request $request) {
		return Response::make()
			->status(Status::NOT_FOUND)
			->body(App::get()->twig->render('404.html'));
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
		return Response::make();
	}

	/**
	 * Handle an exception occurring during the request handling.
	 *
	 * @param Exception $exception The exception.
	 *
	 * @return Response
	 */
	protected function exception(Exception $exception) {
		return Response::make()
			->status(Status::INTERNAL_SERVER_ERROR)
			->body(App::get()->twig->render('500.html', ['exception' => $exception]));
	}

}