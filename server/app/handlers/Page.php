<?php

namespace app\handlers;

use \tempest\routing\Response;


class Page extends Response
{

	protected function send($request)
	{
		return $request->get("hello");
	}

}