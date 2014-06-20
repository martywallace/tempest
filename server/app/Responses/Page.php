<?php namespace Responses;

use Tempest\Routing\Response;
use Tempest\Routing\Request;
use Tempest\Templating\Template;
use Tempest\MySQL\Database;


class Page extends Response
{

	public function index(Request $request)
	{
		$this->mime = 'text/html';

		$db = new Database("localhost", "test", "root", "");

		$template = Template::load("/templates/base.html")
			->bind(["content" => Template::load("/templates/intro.html")]);

		return $template;
	}

}