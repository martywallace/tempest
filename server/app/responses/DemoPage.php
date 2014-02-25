<?php

namespace app\responses;

use \tempest\routing\Response;
use \tempest\routing\Request;
use \tempest\templating\Template;


class DemoPage extends Response
{

	protected function setup()
	{
		$this->setMime(MIME_TEXT);
	}


	protected function respond(Request $request)
	{
		$html = Template::load("template.html");
		$html = Template::merge($html, array(
			"first" => $request->param(GET, 'first', 'Marty'),
			"last" => $request->param(GET, 'last', 'Wallace')
		));

		return $html;
	}

}