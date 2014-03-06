<?php

namespace tempest\routing;

use \tempest\routing\Request;


abstract class Response
{

	private $app;
	private $reqiest;
	private $mime = MIME_HTML;


	public function __construct($app, $request)
	{
		$this->app = $app;
		$this->request = $request;
		$this->setup();
	}


	protected function setup(){ /* Virtual */ }


	public function getApp(){ return $this->app; }
	public function getMime(){ return $this->mime; }
	public function getRequest(){ return $this->request; }
	public function setMime($value){ $this->mime = $value; }

}