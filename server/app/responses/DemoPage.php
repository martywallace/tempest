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


	public function index(Request $request)
	{
		$html = Template::load("demo.html");

		for($i = 0; $i < 10000; $i++)
		{
			$html = Template::inject($html, array(
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

			$html = Template::inject($html, new DemoModel(), 'demo');
			$html = Template::inject($html, new DemoModel());
		}


		return $html;
	}


	public function about(Request $request)
	{
		return 'About page.';
	}

}