<?php namespace Responses;

use Tempest\Routing\Response;
use Tempest\Routing\Request;
use Tempest\Templating\Template;


class Page extends Response
{

	public function index(Request $request)
	{
		if($request->getFormat() === Request::FORMAT_NONE)
		{
			return Template::load("/templates/base.html")
				->bind(["content" => Template::load("/templates/intro.html")]);
		}
	}

}