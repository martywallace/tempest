<?php namespace Tempest\Routing;

use Tempest\Routing\Request;


class Response
{

	protected $mime = 'text/plain';


	public function setup(){ /**/ }
	public function index(Request $request){ /**/ }


	public function getMime(){ return $this->mime; }

}