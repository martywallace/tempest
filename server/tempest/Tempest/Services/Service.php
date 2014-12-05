<?php namespace Tempest\Services;

use Tempest\Tempest;


class Service
{

	private $app;


	public function __construct(Tempest $app)
	{
		$this->app = $app;
	}


	protected function getApp(){ return $this->app; }


	public function getServiceName(){ return null; }

}