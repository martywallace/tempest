<?php

namespace tempest\routing;

use \tempest\routing\Request;


class Response
{

	private $app;
	protected $mime = MIME_HTML;


	public function __construct($app)
	{
		$this->app = $app;
	}


	protected function setup(){ /* Virtual */ }
	protected function send(Request $request){ /* Virtual */ }


	public function getApp(){ return $this->app; }
	public function getMime(){ return $this->mime; }
	public function getOutput(){ return $this->send($this->app->getRouter()->getRequest()); }

}