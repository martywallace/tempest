<?php

namespace app\handlers;

use \tempest\routing\Response;
use \tempest\routing\Request;


class Page extends Response
{

	protected function setup()
	{
		$this->setMime(MIME_TEXT);
	}


	protected function respond(Request $request)
	{
		return $request->param(NAMED, 'x', 'Homepage');
	}

}