<?php

namespace tempest\routing;


class Handler
{

	private $application;
	protected $mime = MIME_HTML;


	public function __construct($application)
	{
		$this->application = $application;
	}


	protected function get(){ /* Virtual */ }
	protected function post(){ /* Virtual */ }


	public function getMime(){ return $this->mime; }
	public function getOutput(){ return call_user_func(array($this, REQUEST_METHOD)); }
	public function getApplication(){ return $this->application; }

}