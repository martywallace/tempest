<?php

namespace tempest\routing;

use \tempest\routing\Router;
use \tempest\routing\Route;


class Request extends Route
{

	private $params = array();


	public function param($type, $name, $default = null)
	{
		switch($type)
		{
			case POST: return isset($_POST[$name]) ? $_POST[$name] : $default; break;
			case GET: return isset($_GET[$name]) ? $_GET[$name] : $default; break;
			case NAMED: return array_key_exists($name, $this->params) ? $this->params[$name] : $default; break;
		}

		return $default;
	}


	public function findMatch(Router $router)
	{
		foreach($router->getRoutes() as $route)
		{
			if(count($route->getParts()) !== count($this->getParts()))
			{
				// Don't bother if the Route lengths don't match.
				// Move to the next Route.
				continue;
			}


			$match = null;
			$params = array();

			// Compare each part of the Route to each part of the Request.
			for($i = 0; $i < count($route->getParts()); $i++)
			{
				$localPart = $this->getPart($i);
				$routePart = $route->getPart($i);

				if($localPart->matches($routePart))
				{
					$match = $route;
					if($routePart->getType() === RoutePart::TYPE_NAMED)
					{
						// Associated named part with request value.
						$params[$routePart->getBase()] = $localPart->getBase();
					}
				}
				else
				{
					// Parts didn't match, abort check.
					$match = null;
					$params = array();

					break;
				}
			}

			if($match !== null)
			{
				$this->params = $params;
				return $match;
			}
		}


		return null;
	}

}