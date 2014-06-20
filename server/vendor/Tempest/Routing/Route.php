<?php namespace Tempest\Routing;

use Tempest\Utils\Path;


class Route extends Path
{
	
	private $handler;


	public function __construct($base, $handler)
	{
		$this->handler = preg_split('/\:+/', $handler);
		parent::__construct($base);
	}


	public function getHandlerClass(){ return $this->handler[0]; }
	public function getHandlerMethod(){ return count($this->handler) > 1 ? $this->handler[1] : 'index'; }

}