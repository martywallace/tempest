<?php namespace App\Responses;

use Tempest\Routing\Response;
use Tempest\Routing\Request;
use Tempest\Templating\Template;


class Test extends Response
{
	
	public function index(Request $request)
	{
		$this->mime = 'text/html';

		$template = Template::load("templates/tempest/" . $request->data(GET, 'page', 'dashboard') . ".html");

		return $template;
	}

}