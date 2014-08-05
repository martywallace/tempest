<?php namespace Responses;

use Tempest\Routing\Response;
use Tempest\Routing\Request;
use Tempest\Templating\Template;
use Tempest\MySQL\Database;


class Tests extends Response
{

	protected $db;


	public function setup(Request $r)
	{
		$this->db = new Database($this->getConfig()->data("db"));
	}


	public function dbGet(Request $r)
	{
		print_r($this->db->prop("SELECT COUNT(*) FROM humans"));
	}


	public function dbInsert(Request $r)
	{
		$this->db->insert('humans', [
			"firstName" => $r->data(GET, 'first'),
			"lastName" => $r->data(GET, 'last')
		]);

		$this->dbGet($r);
	}

}