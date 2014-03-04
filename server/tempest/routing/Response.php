<?php

namespace tempest\routing;

use \tempest\routing\Request;


abstract class Response
{

	private $app;
	private $mime = MIME_HTML;


	public function __construct($app)
	{
		$this->app = $app;
		$this->setup();
	}


	protected function setup(){ /* Virtual */ }


	public function getApp(){ return $this->app; }
	public function getMime(){ return $this->mime; }
	public function setMime($value){ $this->mime = $value; }

}