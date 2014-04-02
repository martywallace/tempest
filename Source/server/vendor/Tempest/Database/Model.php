<?php namespace Tempest\Database;

use Tempest\Database\Repository;


class Model
{

	protected $table = null;
	protected $primary = 'id';


	public static function find($primary, $columns = "*")
	{
		$def = new static();
		$stmt = Repository::prepare("SELECT $columns FROM {$def->table} WHERE {$def->primary} = :primary");
		$stmt->execute(array(":primary" => $primary));

		$result = $stmt->fetchObject(get_class($def));

		if($result === false)
		{
			Repository::error($stmt, "Could not find an instance of '" . get_class($def) . "' where '{$def->primary}' is '$primary'");
			return null;
		}

		return $result;
	}


	public static function create(Array $primitive)
	{
		$def = new static();

		$columns = array_keys($primitive);
		$values = array_values($primitive);
		$placeholders = array_map(function($x){ return ":$x"; }, $columns);

		$stmt = Repository::prepare("INSERT INTO {$def->table} (" . implode(',',$columns) . ") VALUES(" . implode(',',$placeholders) . ")");
		
		if($stmt->execute(array_combine($placeholders, $values)))
		{
			// Return newly created Model if successful.
			return static::find(Repository::lastInsertId());
		}
		else
		{
			// Error with create request.
			Repository::error($stmt, "Could not create an instance of '" . get_class($def) ."'");
		}

		return null;
	}


	public function save()
	{
		$stmt = Repository::prepare("INSERT INTO {$this->table} ({}) VALUES({})
			ON DUPLICATE KEY UPDATE {}");

		$stmt->execute(array());

		if(in_array('id', $this->columns) && $this->id === null)
		{
			// Assign ID.
			$this->id = Repository::lastInsertId();
		}
	}

}