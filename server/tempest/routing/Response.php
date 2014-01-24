<?php

namespace tempest\routing;


class Response
{

	private $app;
	protected $mime = MIME_HTML;


	public function __construct($app)
	{
		$this->app = $app;
	}


	protected function setup(){ /* Virtual */ }
	protected function send(){ /* Virtual */ }


	public function getMime(){ return $this->mime; }
	public function getOutput(){ return $this->send(); }
	public function getApp(){ return $this->app; }

}