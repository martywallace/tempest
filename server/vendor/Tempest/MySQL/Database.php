<?php namespace Tempest\MySQL;

use PDO;
use PDOStatement;


class Database extends PDO
{

	public function __construct(Array $connection)
	{
		$success = true;
		$required = ["host", "dbname", "user", "pass"];

		foreach($required as $r)
		{
			if(!array_key_exists($r, $connection))
			{
				$success = false;
				trigger_error("The connection array provided to <code>Database->__construct()</code> requires the keys <code>" . implode(', ', $required) . "</code>.");
				break;
			}
		}

		if($success)
			parent::__construct("mysql:host={$connection['host']};dbname={$connection['dbname']}", $connection["user"], $connection["pass"]);
	}

}