<?php namespace Tempest\HTTP;

use Tempest\HTTP\Route;
use Tempest\HTTP\Request;


/**
 * The Router directs a Request to the relevant Response.
 * @author Marty Wallace.
 */
class Router
{

	private $request;
	private $match;
	private $params = array();


	/**
	 * Constructor.
	 */
	public function __construct()
	{
		$this->request = new Request($this);
	}

	
	/**
	 * Registers the Routes that will be managed by this Router.
	 * @param $routes Array An Array of Routes.
	 */
	public function register(Array $routes)
	{
		$possible = array();

		foreach($routes as $input => $handler)
		{
			$route = new Route($input, $handler);
			$score = 0;
			$named = array();

			if($this->request->getLength() === 0 && $route->getLength() === 0)
			{
				// Exact match on index route.
				$score = 1;
			}


			if($this->request->getLength() !== $route->getLength())
			{
				// Route lengths do not match, move onto the next route.
				continue;
			}


			// Compare each chunk in the request path with each chunk in the route.
			for($i = 0; $i < $this->request->getLength(); $i++)
			{
				$chunkA = $this->request->chunk($i);
				$chunkB = $route->chunk($i);

				if($chunkA === $chunkB)
				{
					// Exact match between request and subject chunk.
					$score += 2;
				}
				else if(trim($chunkB, '[]') !== $chunkB)
				{
					// Chunk is dynamic.
					$score += 1;
					$named[trim($chunkB, '[]')] = $chunkA;
				}
				else
				{
					// Chunks are not relevant enough.
					continue 2;
				}
			}


			if($score > 0)
			{
				// Route has a positive score, place in list of possible routes.
				$possible[$score][] = array($route, $named);
			}
		}


		if(count($possible) > 0)
		{
			$top = max(array_keys($possible));
			$top = $possible[$top];

			if(count($top) === 1)
			{
				// Could match a valid route - success!
				$this->match = $top[0][0];
				$this->params = $top[0][1];
			}

			else if(count($top) > 1)
			{
				// Multiple possible routes match the request.
				trigger_error("Ambiguous request.");
			}
		}
	}


	/**
	 * Returns the current Request.
	 */
	public function getRequest(){ return $this->request; }


	/**
	 * Returns the matched Route.
	 */
	public function getMatch(){ return $this->match; }


	/**
	 * Returns the named parameters and values for this Request, if any.
	 */
	public function getParams(){ return $this->params; }

}