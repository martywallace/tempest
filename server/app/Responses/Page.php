<?php namespace Responses;

use Tempest\Routing\Response;
use Tempest\Routing\Request;
use Tempest\Templating\Template;


class Page extends Response
{

	public function index(Request $request)
	{
		trigger_error("sup fef esfds fds");
		trigger_error("sup fef esfds fds");
		
		return Template::load("/templates/base.html")
			->bind(["content" => Template::load("/templates/intro.html")]);
	}

}