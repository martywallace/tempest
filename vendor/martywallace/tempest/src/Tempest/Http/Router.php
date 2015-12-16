<?php namespace Tempest\Http;

use Exception;
use FastRoute\Dispatcher;
use FastRoute\RouteCollector;

/**
 * The application router.
 *
 * @property-read string $method The request method e.g. GET, POST.
 * @property-read string $uri The request URI.
 * @property-read Route $matched The matched route being triggered.
 *
 * @package Tempest\Http
 * @author Marty Wallace
 */
class Router {

	/** @var Route[] */
	private $_routes = array();

	/** @var Route */
	private $_matched;

	/** @var mixed[] */
	private $_instantiated = array();

	public function add(Route $route) {
		$this->_routes[] = $route;
	}

	public function __get($prop) {
		if ($prop === 'method') return strtoupper($_SERVER['REQUEST_METHOD']);
		if ($prop === 'uri') return parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
		if ($prop === 'matched') return $this->_matched;

		return null;
	}

	public function __isset($prop) {
		return property_exists($this, $prop) ||
			$this->{$prop} !== null;
	}

	public function dispatch() {
		$response = new Response();

		$dispatcher = \FastRoute\simpleDispatcher(function(RouteCollector $collector) {
			foreach ($this->_routes as $route) {
				$collector->addRoute($route->method, $route->route, $route);
			}
		});

		$info = $dispatcher->dispatch($this->method, $this->uri);

		if ($info[0] === Dispatcher::FOUND) {
			// Successful route match.
			$request = new Request($info[2]);
			$this->_matched = $info[1];

			$respond = true;

			if (count($this->_matched->middleware) > 0) {
				foreach ($this->_matched->middleware as $middleware) {
					if (!$this->instantiateAndCall($middleware, $request, $response)) {
						$respond = false;
						break;
					}
				}
			}

			if ($respond) {
				$response->body = $this->instantiateAndCall($this->_matched->handler, $request, $response);
			}
		}

		if ($info[0] === Dispatcher::NOT_FOUND) {
			$response->status = Status::NOT_FOUND;

			if (app()->twig->loader->exists('404.html')) $response->body = app()->twig->render('404.html');
			else $response->body = app()->twig->render('@tempest/404.html');
		}

		if ($info[0] === Dispatcher::METHOD_NOT_ALLOWED) {
			$response->status = Status::METHOD_NOT_ALLOWED;
			$response->body = app()->twig->render('@tempest/405.html');
		}

		$response->send();
	}

	/**
	 * Instantiate an instance of a Controller or Middleware and call a method attached to that class, passing the
	 * current Request and Response objects to that method. The created instance is stored for future method calls (e.g.
	 * if the same middleware class has multiple methods that are called in one chain, only one instance of that
	 * middleware is actually created.
	 *
	 * @param string $handler The handler used to reference the class and method within that class to call. If no method
	 * detail is provided, the default is index. The handler follows the format "ClassName" to target a class and call a
	 * method "index" or "ClassName::methodName" to target a specific method of that class.
	 * @param Request $request The request object to pass to the method.
	 * @param Response $response The response object to pass to the method.
	 *
	 * @return mixed The result of calling the class method.
	 *
	 * @throws Exception If the class or method does not exist or the class does not inherit Controller or Middleware.
	 */
	public function instantiateAndCall($handler, Request $request = null, Response $response = null) {
		$handler = explode('::', $handler);

		$class = '\\' . trim($handler[0], '\\');
		$method = count($handler) > 1 ? $handler[1] : 'index';
		$instance = null;

		if (array_key_exists($class, $this->_instantiated)) {
			$instance = $this->_instantiated[$class];
		}

		if (empty($instance) && class_exists($class)) {
			$instance = new $class();
			$this->_instantiated[$class] = $instance;
		}

		if (!empty($instance)) {
			if (is_a($instance, 'Tempest\Http\Controller') || is_a($instance, 'Tempest\Http\Middleware')) {
				if (method_exists($instance, $method)) {
					return $instance->{$method}($request, $response);
				} else {
					throw new Exception('Class "' . $class . '" does not define a method "' . $method . '".');
				}
			} else {
				throw new Exception('Class "' . $class . '" is not an instance of Controller or Middleware.');
			}
		} else {
			throw new Exception('Class "' . $class . '" does not exist.');
		}
	}

}