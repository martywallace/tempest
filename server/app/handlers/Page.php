<?php

namespace app\handlers;

use \tempest\routing\Response;
use \tempest\templating\Template;
use \app\models\SampleModel;


class Page extends Response
{

	public $name = 'Marty Wallace';
	public $title = 'Hello world!';
	public $marty;


	protected function setup()
	{
		$this->marty = new SampleModel();
	}


	protected function respond($request)
	{
		$template = new Template("template.html");
		$template->update($this);

		return $template;
	}


	public function getTime()
	{
		return date("Y", time());
	}


	public $person = array("name" => "Steve");

}