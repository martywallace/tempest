<?php namespace Responses;

use Tempest\Routing\Response;
use Tempest\Routing\Request;
use Tempest\Templating\Template;
use Tempest\Utils\JSONResult;
use Tempest\Utils\ResultError;


class Page extends Response
{

	public function index(Request $request)
	{
		return Template::load("/templates/base.html")
			->bind(["content" => Template::load("/templates/intro.html")]);
	}

}