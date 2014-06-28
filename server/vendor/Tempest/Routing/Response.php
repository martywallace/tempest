<?php namespace Tempest\Routing;

use Tempest\Base\Tempest;
use Tempest\Routing\Request;


class Response
{

	private $app;
	private $mime = MIME_HTML;


	public function __construct(Tempest $app)
	{
		$this->app = $app;
	}


	public function setup(Request $request){ /**/ }
	public function index(Request $request){ /**/ }


	public function getMime(){ return $this->mime; }
	public function setMime($value){ $this->mime = $value; }

	public function getApp(){ return $this->app; }
	public function getRequest(){ return $this->app->getRouter()->getRequest(); }
	public function getNamedJSON(){ return json_encode($this->getRequest()->data(NAMED)); }

}