<?php

namespace tempest\routing;


class Handler
{

	protected $mime = MIME_HTML;
	protected $output;


	public function __construct()
	{
		//
	}


	protected function get()
	{
		// Virtual.
	}


	protected function post()
	{
		// Virtual.
	}


	public function getMime(){ return $this->mime; }
	public function getOutput(){ return call_user_func(array($this, REQUEST_METHOD)); }

}