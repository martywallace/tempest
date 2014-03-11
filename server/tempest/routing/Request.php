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
		$possibleRoutes = array();
		foreach($router->getRoutes() as $route)
		{
			$totalScore = 0;
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
				$score = $localPart->compare($routePart);

				if($score > 0)
				{
					$match = $route;
					$totalScore += $score;

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
				$possibleRoutes[] = array("score" => $totalScore, "match" => $match);
			}
		}


		usort($possibleRoutes, array($this, 'sortPossibleRoutes'));

		if(count($possibleRoutes) === 0)
		{
			// No routes were matched.
			return null;
		}
		if(count($possibleRoutes) === 1 || (count($possibleRoutes) > 1 && $possibleRoutes[0]["score"] !== $possibleRoutes[1]["score"]))
		{
			// One or more routes where matched, but the highest scorer had a unique score.
			$matchedRoute = array_shift($possibleRoutes);
			return $matchedRoute["match"];
		}
		else
		{
			trigger_error("Ambiguous request - multiple routes matched.");
		}


		return null;
	}


	public function redirect($url)
	{
		header("Location: " . CLIENT_ROOT . $url);
		exit();
	}


	private function sortPossibleRoutes($a, $b)
	{
		return $a["score"] < $b["score"];
	}

}