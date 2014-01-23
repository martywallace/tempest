<?php

namespace tempest\routing;

use \tempest\routing\Route;

class Router
{

	private $default;
	private $routes = array();


	public function map($map, $default = "home")
	{
		foreach($map as $pattern => $handler)
		{
			$route = new Route($pattern, $handler);
			$this->routes[] = $route;

			if($route->getPattern() === $default)
			{
				$this->default = $route;
			}
		}
	}


	public function getRoute()
	{
		if(strlen(REQUEST_URI) === 0)
		{
			// Use default Route if the request is to the site root.
			return $this->default;
		}

		foreach($this->routes as $route)
		{
			$pattern = $route->getPattern();
			if($pattern === REQUEST_URI)
			{
				return $route;
			}
		}

		return null;
	}


	public function getDefault(){ return $this->default; }
	public function getRoutes(){ return $this->routes; }

}