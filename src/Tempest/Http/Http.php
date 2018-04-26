<?php namespace Tempest\Http;

use Closure;
use Tempest\Services\TwigService;
use Throwable;
use Tempest\App;
use Tempest\Exceptions\HttpException;
use Tempest\Http\Modes\ActionMode;
use Tempest\Http\Modes\RenderMode;
use Tempest\Kernel\Kernel;
use Tempest\Kernel\Input;
use Tempest\Events\ExceptionEvent;
use Tempest\Http\Session\BaseSessionHandler;
use Tempest\Http\Session\SessionDirective;
use Tempest\Http\Middleware\MiddlewarePointer;
use Tempest\Validation\ValidationException;
use FastRoute\RouteCollector;
use FastRoute\Dispatcher;

/**
 * The HTTP kernel deals with interpreting a HTTP {@link Request request} and
 * generating a {@link Response response}.
 *
 * @author Ascension Web Development
 */
class Http extends Kernel implements HasMiddleware {

	/** @var Route[] */
	private $routes;

	/** @var MiddlewarePointer[] */
	private $middleware = [];

	/** @var BaseSessionHandler */
	private $sessionHandler;

	/**
	 * Http constructor.
	 *
	 * @param callable|string $routes Known routes to match the request against.
	 * Can either be a function accepting this HTTP instance or a string
	 * pointing to a PHP file that returns a function accepting this HTTP
	 * instance.
	 */
	public function __construct($routes) {
		parent::__construct($routes);

		$root = new Group();

		if ($this->getConfig()) {
			// Attach the routes to the root group.
			$root->add($this->getConfig());
		}

		$this->routes = $root->flatten();
	}

	/**
	 * Dump output as a HTTP response.
	 *
	 * @param mixed $data The data to debug.
	 * @param string $format The debugging format.
	 *
	 * @return Response
	 */
	public function dump($data, $format = App::DUMP_FORMAT_PRINT_R): Response {
		return Response::make()
			->setHeader(
				Header::CONTENT_TYPE,
				$format === App::DUMP_FORMAT_JSON
					? ContentType::APPLICATION_JSON
					: ContentType::TEXT_PLAIN
			)
			->setBody(parent::dump($data, $format));
	}

	/**
	 * Handle an incoming {@link Request HTTP request} and generate a
	 * {@link Response response} for sending.
	 *
	 * @param Request|Input $request The request to handle.
	 *
	 * @return Response
	 */
	public function handle(Input $request): Response {
		$response = Response::make();

		/** @var TwigService $twig */
		$twig = App::get()->getContainer()->get(TwigService::class);

		// Bind the request and response to Twig.
		$twig->addGlobal('request', $request);
		$twig->addGlobal('response', $response);

		// If sessions are enabled, attach some information from the request to it.
		if ($this->sessionHandler) {
			$this->sessionHandler->attachRequest($request);
		}

		try {
			// Attempt to match a route.
			$info = \FastRoute\simpleDispatcher(function (RouteCollector $collector) {
				foreach ($this->routes as $route) {
					$collector->addRoute($route->getMethods(), $route->getUri(), $route);
				}
			})->dispatch($request->getMethod(), $request->getUri());

			if ($info[0] === Dispatcher::FOUND) $this->found($request, $response, $info[1], $info[2]);
			else if ($info[0] === Dispatcher::NOT_FOUND) $this->notFound($response);
			else if ($info[0] === Dispatcher::METHOD_NOT_ALLOWED) $this->methodNotAllowed($request, $response, $info[1]);

		} catch (ValidationException $exception) {
			$response->setStatus(Status::BAD_REQUEST)->json([
				'message' => $exception->getMessage(),
				'fields' => $exception->getErrors()
			]);

		} catch (Throwable $exception) {
			$this->dispatch(ExceptionEvent::EXCEPTION, new ExceptionEvent($exception));

			$response->setStatus(Status::INTERNAL_SERVER_ERROR)->render('500.html', [
				'exception' => $exception
			]);
		}

		return $response;
	}

	public function addMiddleware(string $middleware, string $method = 'index', array $options = []) {
		$this->middleware[] = new MiddlewarePointer($middleware, $method, $options);

		return $this;
	}

	/**
	 * Create a new {@link Route route}.
	 *
	 * @param string|string[] $methods The HTTP method(s) to associate this
	 * route with.
	 * @param string $uri The URI that will trigger this route.
	 *
	 * @return Route
	 */
	public function route(array $methods, string $uri): Route {
		if (!is_array($methods)) {
			$methods = [$methods];
		}

		$methods = array_map(function($method) {
			// Uppercase all method names.
			return strtoupper($method);
		}, $methods);

		return new Route($methods, $uri);
	}

	/**
	 * Create a new {@link Route route} with its method set to GET.
	 *
	 * @param string $uri The URI that will trigger this route.
	 *
	 * @return Route
	 */
	public function get(string $uri): Route {
		return $this->route([Method::GET], $uri);
	}

	/**
	 * Create a new {@link Route route} with its method set to POST.
	 *
	 * @param string $uri The URI that will trigger this route.
	 *
	 * @return Route
	 */
	public function post(string $uri): Route {
		return $this->route([Method::POST], $uri);
	}

	/**
	 * Create a new {@link Route route} with its method set to PUT.
	 *
	 * @param string $uri The URI that will trigger this route.
	 *
	 * @return Route
	 */
	public function put(string $uri): Route {
		return $this->route(Method::PUT, $uri);
	}

	/**
	 * Create a new {@link Route route} with its method set to PATCH.
	 *
	 * @param string $uri The URI that will trigger this route.
	 *
	 * @return Route
	 */
	public function patch(string $uri): Route {
		return $this->route([Method::PATCH], $uri);
	}

	/**
	 * Create a new {@link Route route} with its method set to DELETE.
	 *
	 * @param string $uri The URI that will trigger this route.
	 *
	 * @return Route
	 */
	public function delete(string $uri): Route {
		return $this->route([Method::DELETE], $uri);
	}

	/**
	 * Create a new {@link Route route} with its method set to HEAD.
	 *
	 * @param string $uri The URI that will trigger this route.
	 *
	 * @return Route
	 */
	public function head(string $uri): Route {
		return $this->route([Method::HEAD], $uri);
	}

	/**
	 * Create a new {@link Route route} with its method set to OPTIONS.
	 *
	 * @param string $uri The URI that will trigger this route.
	 *
	 * @return Route
	 */
	public function options(string $uri): Route {
		return $this->route([Method::OPTIONS], $uri);
	}

	/**
	 * Create a new {@link Group group of routes} that will be
	 * {@link Group::flatten flattened down} recursively.
	 *
	 * @param string $uri The base URI that will be merged onto the head of each
	 * descendant route or group.
	 * @param Route[]|Group[] $routes One or more child routes or groups.
	 *
	 * @return Group
	 */
	public function group(string $uri, array $routes): Group {
		return new Group($uri, $routes);
	}

	/**
	 * Enable HTTP sessions, beginning a new one if there is not one already.
	 *
	 * @param BaseSessionHandler $handler The handler responsible for managing
	 * the sessions.
	 * @param array $directives The session {@link SessionDirective directives}.
	 *
	 * @return self
	 *
	 * @throws HttpException If sessions are not enabled.
	 * @throws HttpException If there is already an active session.
	 * @throws HttpException If the session could not be successfully started.
	 */
	public function enableSessions(BaseSessionHandler $handler, array $directives = []): self {
		if (session_status() === PHP_SESSION_DISABLED) {
			throw new HttpException(HttpException::SESSION_DISABLED);
		}

		if (session_status() === PHP_SESSION_ACTIVE) {
			throw new HttpException(HttpException::SESSION_ALREADY_STARTED);
		}

		$this->sessionHandler = $handler;

		session_set_save_handler($handler, true);

		$success = session_start(array_merge([
			SessionDirective::NAME => 'SessionID',
			SessionDirective::USE_COOKIES => true,
			SessionDirective::USE_ONLY_COOKIES => true,
			SessionDirective::COOKIE_HTTPONLY => true
		], $directives));

		if (!$success) {
			throw new HttpException(HttpException::SESSION_COULD_NOT_ENABLE);
		}

		return $this;
	}

	/**
	 * Handle a successfully matched route.
	 *
	 * @param Request $request The request being handled.
	 * @param Response $response The response to be sent.
	 * @param Route $route The route that was matched.
	 * @param mixed[] $named Named arguments provided in the request, defined by
	 * the route.
	 *
	 * @throws HttpException If the matched route does not perform any valid
	 * action.
	 */
	protected function found(Request $request, Response $response, Route $route, array $named): void {
		foreach ($named as $property => $value) {
			// Attached all named route data.
			$request->attachParam($property, $value);
		}

		$mode = $route->getMode();

		if (empty($mode)) {
			throw new HttpException(sprintf(HttpException::ROUTE_NO_MODE, $route->getUri()));
		}

		if ($mode instanceof ActionMode || $mode instanceof RenderMode) {
			// The request resolution (last function to be called in the pipeline).
			$resolution = function() use ($mode, $request, $response) {
				if ($mode instanceof RenderMode) {
					$response->render($mode->getTemplate(), $mode->getContext());
				}

				if ($mode instanceof ActionMode) {
					if (!class_exists($mode->getController())) {
						throw new HttpException(sprintf(HttpException::CONTROLLER_DOES_NOT_EXIST, $mode->getController()));
					}

					$controllerClass = $mode->getController();
					$controller = new $controllerClass($mode->getOptions());

					if (!method_exists($controller, $mode->getMethod())) {
						throw new HttpException(sprintf(HttpException::CONTROLLER_DOES_NOT_DEFINE_METHOD, $mode->getController(), $mode->getMethod()));
					}

					$controller->{$mode->getMethod()}($request, $response);
				}
			};

			$pipeline = array_merge(
				$this->middleware,
				$route->getMiddleware(),
				[$resolution]
			);

			$pipeline = array_map(function($action) use ($request, $response, $resolution) {
				if ($action !== $resolution) {
					if ($action instanceof MiddlewarePointer) {
						if (!class_exists($action->getMiddleware())) {
							throw new HttpException(sprintf(HttpException::MIDDLEWARE_DOES_NOT_EXIST, $action->getMiddleware()));
						}

						$middlewareClass = $action->getMiddleware();
						$middleware = new $middlewareClass($action->getOptions());

						if (!method_exists($middleware, $action->getMethod())) {
							throw new HttpException(sprintf(HttpException::MIDDLEWARE_DOES_NOT_DEFINE_METHOD, $action->getMiddleware(), $action->getMethod()));
						}

						return Closure::fromCallable([$middleware, $action->getMethod()]);
					}
				}

				return $action;
			}, $pipeline);

			// Bind all next closures and call the first.
			$pipeline = $this->bindNext($pipeline, $request, $response);
			$pipeline();
		}
	}

	/**
	 * Handle no route match.
	 *
	 * @param Response $response The response to be sent.
	 */
	protected function notFound(Response $response): void {
		$response
			->setStatus(Status::NOT_FOUND)
			->render('404.html');
	}

	/**
	 * Handle a matched route with an unsupported method.
	 *
	 * @param Request $request The request being handled.
	 * @param Response $response The response to be sent.
	 * @param string[] $allowed The allowed methods.
	 */
	protected function methodNotAllowed(Request $request, Response $response, array $allowed): void {
		$response
			->setStatus(Status::METHOD_NOT_ALLOWED)
			->setHeader(Header::ALLOW, implode(', ', $allowed))
			->render('405.html', [
				'method' => $request->getMethod(),
				'allowed' => $allowed
			]);
	}

	/**
	 * Recursively bind all input closures with a pointer to the next one in line, then return the first closure.
	 *
	 * @param Closure[] $pipeline THe closure pipeline.
	 * @param Request $request The request to push through the pipeline.
	 * @param Response $response The response to push through the pipeline.
	 * @param int $index The pipeline index to work from.
	 *
	 * @return Closure
	 */
	private function bindNext(array $pipeline, Request $request, Response $response, $index = 0): Closure {
		$closure = $pipeline[$index];

		if ($index === count($pipeline) - 1) {
			// The last item doesn't need to point to a subsequent closure.
			return $closure;
		} else {
			return function() use ($closure, $pipeline, $request, $response, $index) {
				$closure($request, $response, $this->bindNext($pipeline, $request, $response, $index + 1));
			};
		}
	}

	/**
	 * Get all registered routes.
	 *
	 * @return Route[]
	 */
	public function getRoutes(): array {
		return $this->routes;
	}

	/**
	 * Get all registered middleware.
	 *
	 * @return mixed[][]
	 */
	public function getMiddleware(): array {
		return $this->middleware;
	}

	/**
	 * Get the active session handler, if sessions were enabled.
	 *
	 * @return BaseSessionHandler
	 */
	public function getSessionHandler(): BaseSessionHandler {
		return $this->sessionHandler;
	}

}