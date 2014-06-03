<?php namespace Tempest\Routing;

use Tempest\Base\Tempest;
use Tempest\Routing\Request;


class Response
{

	private $app;
	protected $mime = 'text/plain';


	public function __construct(Tempest $app)
	{
		$this->app = $app;
	}


	public function setup(Request $request){ /**/ }
	public function index(Request $request){ /**/ }


	public function getMime(){ return $this->mime; }
	public function getApp(){ return $this->app; }

}