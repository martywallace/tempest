<?php namespace Tempest\HTTP;

use Tempest\Utils\Path;


/**
 * Defines a Route.
 * @author Marty Wallace.
 */
class Route extends Path
{

	private $vars;
	private $controller;


	/**
	 * Constructor.
	 *
	 * @param string $base The base path.
	 * @param array $detail The data attached to the route URI.
	 */
	public function __construct($base, Array $detail)
	{
		if (!array_key_exists('controller', $detail))
		{
			trigger_error('Route definitions must point to a controller.');
		}
		else
		{
			$this->vars = array_key_exists('vars', $detail) ? $detail['vars'] : array();
			$this->controller = preg_split('/\:+/', $detail['controller']);
		}

		parent::__construct($base);
	}


	/**
	 * Returns custom variables associated with the route definition.
	 *
	 * @return array
	 */
	public function getVars(){ return $this->vars; }


	/**
	 * Returns the response class handing this Route.
	 *
	 * @return string
	 */
	public function getControllerClass(){ return $this->controller[0]; }


	/**
	 * Returns the method within the response class handling this Route.
	 *
	 * @return string
	 */
	public function getControllerAction(){ return count($this->controller) > 1 ? $this->controller[1] : 'index'; }

}