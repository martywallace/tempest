<?php

namespace app\handlers;

use \tempest\routing\Response;
use \tempest\routing\Request;


class Page extends Response
{

	protected function respond(Request $request)
	{
		return APP_ROOT . "<br>" . CLIENT_ROOT;
	}

}