<?php namespace Responses;

use Tempest\Routing\Response;
use Tempest\Routing\Request;
use Tempest\Templating\Template;
use Tempest\MySQL\Database;


class Page extends Response
{

	public function setup(Request $request)
	{
		$this->setMime(MIME_HTML);
	}


	public function index(Request $request)
	{
		$db = new Database("localhost", "test", "root", "");

		return Template::load("/templates/base.html")->bind(["content" => Template::load("/templates/intro.html")]);
	}

}