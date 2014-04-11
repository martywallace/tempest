<?php namespace Tempest\Routing;

use Tempest\Routing\Request;


class Response
{

	public function index(Request $request)
	{
		return Request::POST;
	}

}