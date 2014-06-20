<?php namespace Responses;

use Tempest\Routing\Response;
use Tempest\Routing\Request;
use Tempest\Templating\Template;
use Tempest\Utils\JSONResponse;


class Page extends Response
{

	public function index(Request $request)
	{
		$this->mime = 'text/html';

		$template = Template::load("/templates/base.html")
			->bind(["content" => Template::load("/templates/intro.html")]);

		return $template;
	}


	public function test(Request $request)
	{
		$this->mime = 'application/json';

		$response = new JSONResponse();
		$response->add("Working");

		return $response;
	}

}