<?php

namespace tempest\routing;

use \tempest\routing\Router;
use \tempest\routing\Route;
use \tempest\routing\RoutePart;


class Request extends Route
{

	private $params = array();


	public function post($field)
	{
		return isset($_POST[$field]) ? $_POST[$field] : null;
	}


	public function get($field)
	{
		return isset($_GET[$field]) ? $_GET[$field] : null;
	}


	public function named($field)
	{
		return array_key_exists($field, $this->params) ? $this->params[$field] : null;
	}


	public function findMatch(Router $router)
	{
		foreach($router->getRoutes() as $route)
		{
			if($route->getTotalParts() !== $this->getTotalParts())
			{
				// Don't bother if the Route lengths don't match.
				// Move to the next Route.
				continue;
			}


			$match = null;
			$params = array();

			// Compare each part of the Route to each part of the Request.
			for($i = 0; $i < $route->getTotalParts(); $i++)
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
				print_r($params);
				$this->params = $params;
				return $match;
			}
		}


		return null;
	}

}