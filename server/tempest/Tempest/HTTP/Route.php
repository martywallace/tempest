<?php namespace Tempest\HTTP;

use Tempest\Utils\Path;


/**
 * Defines a Route.
 * @author Marty Wallace.
 */
class Route extends Path
{
	
	private $handler;


	/**
	 * Constructor.
	 * @param $base The base path.
	 * @param $handler The handler associated with this Route.
	 */
	public function __construct($base, $handler)
	{
		$this->handler = preg_split('/\:+/', $handler);
		parent::__construct($base);
	}


	/**
	 * Returns the response class handing this Route.
	 */
	public function getHandlerClass(){ return $this->handler[0]; }


	/**
	 * Returns the method within the response class handling this Route.
	 */
	public function getHandlerMethod(){ return count($this->handler) > 1 ? $this->handler[1] : 'index'; }

}