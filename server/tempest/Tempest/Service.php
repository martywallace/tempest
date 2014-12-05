<?php namespace Tempest;


/**
 * Defines an application service.
 *
 * @author Marty Wallace.
 */
class Service
{

	private $app;


	public function __construct(Tempest $app)
	{
		$this->app = $app;
	}


	protected function getApp(){ return $this->app; }

}