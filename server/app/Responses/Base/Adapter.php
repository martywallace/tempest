<?php namespace Responses\Base;

use Tempest\HTTP\Request;
use Tempest\HTTP\Response;


class Adapter extends Response
{

	public function setup(Request $r)
	{
		// A good place to set up a database connection that will be used by all handlers
		// in the application, or other general site-wide data.
	}

}