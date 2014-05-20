<?php namespace Tempest\Data;

use PDO;
use Tempest\Data\Table;


class Database extends PDO
{

	public function __construct($host, $dbname, $user, $pass)
	{
		parent::__construct("mysql:host=$host;dbname=$dbname", $user, $pass);
	}

}