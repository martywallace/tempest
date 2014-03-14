<?php

namespace app\responses;

use \tempest\routing\Response;
use \tempest\routing\Request;
use \tempest\templating\Template;
use \app\models\DemoModel;


class DemoPage extends Response
{

	public function index(Request $request)
	{
		$html = Template::load("demo.html");

		for($i = 0; $i < 100; $i++)
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
				),
				"escaped" => "<strong>Hello</strong>",
				"time" => time()
			));

			$html = Template::inject($html, new DemoModel(), 'demo');
			$html = Template::inject($html, new DemoModel());


			$list = array(
				array("name" => "a"),
				array("name" => "b"),
				array("name" => "c"),
				array("name" => "d"),
				array("name" => "e")
			);

			$batch = Template::batch("<li>{{ name }} {{ test }}</li>", $list, 'None');
			$html = Template::inject($html, array("items" => $batch), 'batch');
		}


		return $html;
	}


	public function about(Request $request)
	{
		$html = Template::load("win/hello.html");
		$html = Template::load("win/ggfgfd.html");
		$html = Template::load("gfgdf.html");
		new \PDO();

		return 'About page.';
	}

}