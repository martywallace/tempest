<?php namespace App\Responses;

use Tempest\Routing\Response;
use Tempest\Routing\Request;
use Tempest\Templating\Template;


class Page extends Response
{

	public function index(Request $request)
	{
		$this->mime = 'text/plain';
		return new Template('Site root: ~/');
	}

}