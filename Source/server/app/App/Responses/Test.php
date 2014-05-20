<?php namespace App\Responses;

use Tempest\Routing\Response;
use Tempest\Routing\Request;
use Tempest\Templating\Template;
use Tempest\Data\Database;
use App\Tables\Humans;


class Test extends Response
{

	protected $db;

	
	public function index(Request $request)
	{
		$this->mime = 'text/plain';
		$this->db = new Database('localhost', 'test', 'root', '');
	}

}