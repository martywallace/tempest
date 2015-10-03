<?php namespace Tempest\Http;

use FastRoute\Dispatcher;
use FastRoute\RouteCollector;

/**
 * The application router.
 *
 * @property-read string $method The request method e.g. GET, POST.
 * @property-read string $uri The request URI.
 *
 * @package Tempest\Http
 * @author Marty Wallace
 */
class Router {

	/** @var array[] */
	private $_routes = [];

	public function add($method, $route, Callable $handler) {
		$this->_routes[] = array(
			'method' => strtoupper($method),
			'route' => $route,
			'handler' => $handler
		);
	}

	public function __get($prop) {
		if ($prop === 'method') return strtoupper($_SERVER['REQUEST_METHOD']);
		if ($prop === 'uri') return parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

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
				$collector->addRoute($route['method'], $route['route'], $route['handler']);
			}
		});

		$info = $dispatcher->dispatch($this->method, $this->uri);

		if ($info[0] === Dispatcher::FOUND) {
			$request = new Request($info[2]);
			$response->body = $info[1][0]->{$info[1][1]}($request, $response);
		}

		if ($info[0] === Dispatcher::NOT_FOUND) {
			$response->status = Status::NOT_FOUND;
			$response->body = app()->twig->render('@tempest/404.html');
		}

		if ($info[0] === Dispatcher::METHOD_NOT_ALLOWED) {
			$response->status = Status::METHOD_NOT_ALLOWED;
			$response->body = app()->twig->render('@tempest/405.html');
		}

		$response->send();
	}

}