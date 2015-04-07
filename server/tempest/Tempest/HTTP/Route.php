<?php namespace Tempest\HTTP;

use Tempest\Utils\Path;


/**
 * Defines a Route.
 * @author Marty Wallace.
 */
class Route extends Path
{

	private $detail;
	private $controller;


	/**
	 * Constructor.
	 *
	 * @param string $base The base path.
	 * @param array $detail The data attached to the route URI.
	 */
	public function __construct($base, Array $detail)
	{
		$this->detail = $detail;

		if (!array_key_exists('controller', $detail))
		{
			trigger_error('Route definitions must point to a controller.');
		}
		else
		{
			$this->controller = preg_split('/\:+/', $detail['controller']);
		}

		parent::__construct(
			Path::create($base)
				->prepend(tempest()->getRoot())
				->setStrategy(Path::DELIMITER_LEFT)
		);
	}


	/**
	 * Returns the route definition array.
	 *
	 * @return array
	 */
	public function getDetail() { return $this->detail; }


	/**
	 * Returns the response class handing this Route.
	 *
	 * @return string
	 */
	public function getControllerClass() { return $this->controller[0]; }


	/**
	 * Returns the method within the response class handling this Route.
	 *
	 * @return string
	 */
	public function getControllerAction() { return count($this->controller) > 1 ? $this->controller[1] : 'index'; }

}