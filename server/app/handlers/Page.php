<?php

namespace app\handlers;

use \tempest\routing\Response;


class Page extends Response
{

	protected function respond($request)
	{
		return "Hello world!";
	}

}