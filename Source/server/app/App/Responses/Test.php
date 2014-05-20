<?php namespace App\Responses;

use Tempest\Routing\Response;
use Tempest\Routing\Request;
use Tempest\Templating\Template;


class Test extends Response
{

	public $title = array("value" => "Hello");
	
	public function index(Request $request)
	{
		$this->mime = 'text/plain';

		$template = Template::load("templates/child.html");

		$template->bind($this);

		

		return $template;
	}

}