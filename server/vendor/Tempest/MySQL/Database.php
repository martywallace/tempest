<?php namespace Tempest\MySQL;

use Tempest\MySQL\Table;
use PDO;


class Database extends PDO
{

	private $tables = [];

	
	public function __construct($host, $db, $user, $pass)
	{
		parent::__construct("mysql:host=$host;dbname=$db", $user, $pass);
		$this->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);

		$stmt = $this->prepare("SHOW TABLES");
		$stmt->execute();

		foreach($stmt->fetchAll(PDO::FETCH_NUM) as $t)
		{
			$this->tables[$t[0]] = new Table($this, $t[0]);
		}
	}


	public function table($name)
	{
		return $this->tables[$name];
	}


	public function getTables(){ return $this->tables; }

}