<?php

namespace app\handlers;

use \tempest\routing\Response;
use \tempest\templating\Template;


class Page extends Response
{

	protected function respond($request)
	{
		$template = new Template("template.html");
		$template->update($this);

		return $template->getContent();
	}


	public function getContent()
	{
		$template = new Template("home.html");
		return $template->getContent();
	}

}