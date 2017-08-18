<?php namespace Tempest\Http;

use Closure;
use Exception;
use Tempest\{
	App, Events\ExceptionEvent, Events\HttpKernelEvent, Kernel
};
use FastRoute\{RouteCollector, Dispatcher};

/**
 * The HTTP kernel deals with interpreting a HTTP {@link Request request} and generating a {@link Response response}.
 *
 * @property-read Route[] $routes The loaded routes to be handled.
 *
 * @author Marty Wallace
 */
class Http extends Kernel {

	/** @var Route[] */
	private $_routes;

	/** @var string[][] */
	private $_middleware = [];

	/**
	 * Http constructor.
	 *
	 * @param callable|string $routes Known routes to match the request against. Can either be a function accepting this
	 * HTTP instance or a string pointing to a PHP file that returns a function accepting this HTTP instance.
	 *
	 * @throws Exception
	 */
	public function __construct($routes) {
		$root = new Group();

		if (!empty($routes)) {
			if (is_callable($routes)) {
				// Function provided directly.
				$root->add($routes($this));
			} else {
				$external = require App::get()->root . DIRECTORY_SEPARATOR . $routes;

				if (!is_callable($external)) {
					throw new Exception('External route files must return a callable that returns an array of routes to handle.');
				}

				$root->add($external($this));
			}
		}

		$this->_routes = $root->flatten();
	}

	public function __get($prop) {
		if ($prop === 'routes') return $this->_routes;

		return null;
	}

	/**
	 * Handle an incoming {@link Request HTTP request} and generate a {@link Response response} for sending.
	 *
	 * @param Request $request The request to handle.
	 *
	 * @return Response
	 */
	public function handle(Request $request) {
		$response = new Response();

		try {
			// Attempt to match a route.
			$info = \FastRoute\simpleDispatcher(function (RouteCollector $collector) {
				foreach ($this->_routes as $route) {
					$collector->addRoute($route->method, $route->uri, $route);
				}
			})->dispatch($request->method, $request->uri);

			if ($info[0] === Dispatcher::FOUND) $this->found($request, $response, $info[1], $info[2]);
			else if ($info[0] === Dispatcher::NOT_FOUND) $this->notFound($request, $response);
			else if ($info[0] === Dispatcher::METHOD_NOT_ALLOWED) $this->methodNotAllowed($request, $response, $info[1]);
		} catch (Exception $exception) {
			$this->dispatch(ExceptionEvent::EXCEPTION, new ExceptionEvent($exception));
			$this->exception($response, $exception);
		}

		return $response;
	}

	/**
	 * Attach middleware to be called before requests resolve to a controller or template.
	 *
	 * @param string $class The middleware class.
	 * @param string $method The method within the middleware class to trigger.
	 *
	 * @return $this
	 */
	public function middleware($class, $method) {
		$this->_middleware[] = [$class, $method];

		return $this;
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
	 * Create a new {@link Group group of routes} that will be {@link Group::flatten flattened down} recursively.
	 *
	 * @param string $uri The base URI that will be merged onto the head of each descendant route or group.
	 * @param Route[]|Group[] $routes One or more child routes or groups.
	 *
	 * @return Group
	 */
	public function group($uri, array $routes) {
		return new Group($uri, $routes);
	}

	/**
	 * Handle a successfully matched route.
	 *
	 * @param Request $request The request being handled.
	 * @param Response $response The response to be sent.
	 * @param Route $route The route that was matched.
	 * @param mixed[] $named Named arguments provided in the request, defined by the route.
	 *
	 * @throws Exception If the matched route does not perform any valid action.
	 */
	protected function found(Request $request, Response $response, Route $route, array $named) {
		foreach ($named as $property => $value) {
			// Attached all named route data.
			$request->attachNamed($property, $value);
		}

		if ($route->getMode() === Route::MODE_UNDETERMINED) {
			throw new Exception('Route "' . $route->uri . '" does not perform a valid action');
		}

		if ($route->getMode() === Route::MODE_TEMPLATE || $route->getMode() === Route::MODE_CONTROLLER) {
			$resolution = function() use ($route, $request, $response) {
				if ($route->getMode() === Route::MODE_TEMPLATE) {
					$response->render($route->getTemplate());
				}

				if ($route->getMode() === Route::MODE_CONTROLLER) {
					$controller = $route->getController();

					if (!class_exists($controller[0])) {
						throw new Exception('Controller class "' . $controller[0] . '" does not exist.');
					}

					$instance = new $controller[0]($request, $response);

					if (!method_exists($instance, $controller[1])) {
						throw new Exception('Controller class "' . $controller[0] . '" does not contain a method "' . $controller[1] . '".');
					}

					$instance->{$controller[1]}();
				}
			};

			$pipeline = array_map(function($detail) use ($request, $response, $resolution) {
				if ($detail !== $resolution) {
					if (!class_exists($detail[0])) {
						throw new Exception('Middleware class "' . $detail[0] . '" does not exist.');
					}

					$middleware = new $detail[0]($request, $response);

					if (!method_exists($middleware, $detail[1])) {
						throw new Exception('Middleware class "' . $detail[0] . '" does not contain a method "' . $detail[1] . '".');
					}

					return Closure::fromCallable([new $detail[0]($request, $response), $detail[1]]);
				}

				return $detail;
			}, array_merge($this->_middleware, $route->getMiddleware(), [$resolution]));

			// Bind all next closures and call the first.
			$this->bindNext($pipeline)();
		}
	}

	/**
	 * Handle no route match.
	 *
	 * @param Request $request The request being handled.
	 * @param Response $response The response to be sent.
	 */
	protected function notFound(Request $request, Response $response) {
		$response->status(Status::NOT_FOUND)->render('404.html');
	}

	/**
	 * Handle a matched route with an unsupported method.
	 *
	 * @param Request $request The request being handled.
	 * @param Response $response The response to be sent.
	 * @param string[] $allowed The allowed methods.
	 */
	protected function methodNotAllowed(Request $request, Response $response, array $allowed) {
		$response->status(Status::METHOD_NOT_ALLOWED)->render('405.html', [
			'method' => $request->method,
			'allowed' => $allowed
		]);
	}

	/**
	 * Handle an exception occurring during the request handling.
	 *
	 * @param Response $response The response to be sent.
	 * @param Exception $exception The exception.
	 */
	protected function exception(Response $response, Exception $exception) {
		$response->status(Status::INTERNAL_SERVER_ERROR)->render('500.html', [
			'exception' => $exception
		]);
	}

	/**
	 * Recursively bind all input closures with a pointer to the next one in line, then return the first closure.
	 *
	 * @param Closure[] $pipeline THe closure pipeline.
	 * @param int $index The pipeline index to work from.
	 *
	 * @return Closure
	 */
	protected function bindNext(array $pipeline, $index = 0) {
		$closure = $pipeline[$index];

		if ($index === count($pipeline) - 1) {
			// The last item doesn't need to point to a subsequent closure.
			return $closure;
		} else {
			return function() use ($closure, $pipeline, $index) {
				$closure($this->bindNext($pipeline, $index + 1));
			};
		}
	}

}