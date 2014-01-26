<?php

namespace app\handlers;

use \tempest\routing\Response;
use \tempest\templating\Template;


class Page extends Response
{

	public $name = 'Marty Wallace';
	public $title = 'Hello world!';


	protected function respond($request)
	{
		$template = new Template("template.html");
		$template->update($this);

		return $template;
	}

}