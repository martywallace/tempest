<?php namespace Tempest\Routing;

use Tempest\Routing\Route;
use Tempest\Routing\Request;


class Router
{

	private $request;
	private $match;
	private $named;


	public function __construct()
	{
		$this->request = new Request();
	}

	
	public function register(Array $routes)
	{
		$scores = array();
		foreach($routes as $input => $handler)
		{
			$route = new Route($input, $handler);
			$score = 0;

			if($this->request->getLength() === 0 && $route->getLength() === 0)
			{
				// Exact match on index route.
				$score = 3;
			}

			if($this->request->getLength() !== $route->getLength())
			{
				// Route lengths must match.
				continue;
			}

			for($i = 0; $i < $this->request->getLength(); $i++)
			{
				$a = $this->request->chunk($i);
				$b = $route->chunk($i);

				// Exact match between request and subject chunk.
				if($a === $b) $score += 2;

				else if(trim($b, '[]') !== $b)
				{
					// Chunk is dynamic.
					$score += 1;
				}

				else $score = 0;
			}

			if($score > 0)
			{
				if(!array_key_exists($score, $scores))
				{
					$scores[$score] = array();
				}

				$scores[$score][] = $route;
			}
		}

		if(count($scores) > 0)
		{
			$top = max(array_keys($scores));

			if(count($scores[$top]) === 1)
			{
				$this->match = $scores[$top][0];
			}

			else if(count($scores[$top]) > 1)
			{
				// Multiple possible routes match the request.
				trigger_error("Ambiguous request.");
			}
		}
	}


	public function getRequest(){ return $this->request; }
	public function getMatch(){ return $this->match; }
	public function getNamed(){ return $this->named; }

}