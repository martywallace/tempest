<?php namespace Tempest\HTTP;

/**
 * The Router directs a Request to the relevant Responder.
 * @author Marty Wallace.
 */
class Router
{

	/**
	 * @var Request
	 */
	private $request;

	/**
	 * @var Route
	 */
	private $match;

	/**
	 * @var Array
	 */
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

			if(count($this->request->segments()) === 0 && count($route->segments()) === 0)
			{
				// Exact match on index route.
				$score = 1;
			}


			if(count($this->request->segments()) !== count($route->segments()))
			{
				// Route lengths do not match, move onto the next route.
				continue;
			}


			// Compare each chunk in the request path with each chunk in the route.
			for($i = 0; $i < count($this->request->segments()); $i++)
			{
				$chunkA = $this->request->segment($i);
				$chunkB = $route->segment($i);

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
	 * @return Request The current Request.
	 */
	public function getRequest(){ return $this->request; }


	/**
	 * @return Route The matched Route.
	 */
	public function getMatch(){ return $this->match; }


	/**
	 * @return Array The named parameters and values for this Request, if any.
	 */
	public function getParams(){ return $this->params; }

}