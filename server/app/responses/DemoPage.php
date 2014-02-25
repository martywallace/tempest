<?php

namespace app\responses;

use \tempest\routing\Response;
use \tempest\routing\Request;
use \tempest\templating\Template;


class DemoPage extends Response
{

	protected function setup()
	{
		$this->setMime(MIME_HTML);
	}


	protected function respond(Request $request)
	{
		$html = Template::load("demo.html");

		$html = Template::merge($html, array(
			"first" => $request->param(GET, 'first', 'Steve'),
			"last" => $request->param(GET, 'last', 'Stevenson'),
			"marty" => array(
				"first" => "Marty",
				"last" => "Wallace",
				"age" => array(
					"years" => 22
				)
			)
		));


		return $html;
	}

}