<?php namespace Responses;

use Tempest\Routing\Response;
use Tempest\Routing\Request;
use Tempest\Templating\Template;
use Tempest\MySQL\Database;


class Page extends Response
{

	protected $db;


	public function setup(Request $request)
	{
		$this->setMime(MIME_HTML);

		$conf = $this->getConfig();

		$this->db = new Database(
			$conf->data("db.host"), $conf->data("db.dbname"), $conf->data("db.user"), $conf->data("db.pass")
		);

		$this->db->table("people")->insert([
			"first" => "John",
			"last" => "Smith"
		]);
	}


	public function index(Request $request)
	{
		$template = Template::load("/templates/base.html")
			->bind(["content" => Template::load("/templates/intro.html")]);

		return $template;
	}

}