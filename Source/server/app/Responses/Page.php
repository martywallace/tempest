<?php namespace Responses;

use Tempest\Routing\Response;
use Tempest\Routing\Request;
use Tempest\Templating\Template;


class Page extends Response
{

	public function index(Request $request)
	{
		$this->mime = 'text/html';

		$output = Template::load("/templates/hw.html");
		
		$output->bind([
			"time" => date("Y M d H:i:s", time()),
			"visits" => $_SESSION["visits"]
		]);

		return $output;
	}

}