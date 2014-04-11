<?php namespace App\Responses;

use Tempest\Routing\Response;
use Tempest\Routing\Request;


class Test extends Response
{
	
	public function index(Request $request)
	{
		return 'hello';
	}

}