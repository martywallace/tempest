<?php

namespace app\responses;

use \tempest\routing\Response;
use \tempest\routing\Request;
use \tempest\templating\Template;
use \app\models\DemoModel;


class DemoPage extends Response
{

	protected function setup()
	{
		$this->setMime(MIME_TEXT);
	}


	protected function respond(Request $request)
	{
		$html = Template::load("demo.html");

		for($i = 0; $i < 100000; $i++)
		{
			$html = Template::injectA($html, array(
				"first" => $request->param(GET, 'first', 'Steve'),
				"last" => $request->param(GET, 'last', 'Stevenson'),
				"marty" => array(
					"first" => "Marty",
					"last" => "Wallace",
					"age" => array(
						"years" => 22
					),
					"model" => new DemoModel()
				)
			));

			$html = Template::injectB($html, 'demo', new DemoModel());
		}


		return $html;
	}

}