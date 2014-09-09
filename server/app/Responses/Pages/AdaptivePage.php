<?php namespace Responses\Pages;

use Responses\Base\HTMLPage;
use Tempest\HTTP\Request;
use Tempest\Templating\Template;


class AdaptivePage extends HTMLPage
{

	protected $name;


	public function setup(Request $r)
	{
		parent::setup($r);

		$this->name = $r->getLength() === 0 ? 'home' : implode('-', $r->getChunks());

		$this->styles[] = "css/layout.css";

		$this->scripts[] = "js/vendor/jquery.min.js";
		$this->scripts[] = "js/vendor/tempest.js";
		$this->scripts[] = "js/application.js";
	}


	public function index(Request $request)
	{
		return Template::load("base.html")->bind(array(
			"content" => Template::load("{$this->name}.html")
		));
	}


	public function getName()
	{
		return $this->name;
	}

}