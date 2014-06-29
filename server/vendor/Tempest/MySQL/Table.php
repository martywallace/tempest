<?php namespace Tempest\MySQL;

class Table
{

	private $db;
	private $name;
	private $primary;

	
	public function __construct(Database $db, $name)
	{
		$this->db = $db;
		$this->name = $name;

		$stmt = $db->prepare("SHOW INDEX FROM $name");
		$stmt->execute();

		$this->primary = $stmt->fetchObject()->Column_name;
	}


	public function prepare($query)
	{
		foreach([
			"{TBL}" => $this->name,
			"{PRI}" => $this->primary
		] as $a => $b)
		{
			$query = str_replace($a, $b, $query);
		}

		return $this->db->prepare($query);
	}


	public function find($primary, Array $fields = null)
	{
		$fields = $fields === null ? '*' : implode(',', $fields);

		$stmt = $this->prepare("SELECT $fields FROM {TBL} WHERE {PRI} = :primary");
		$stmt->execute([":primary" => $primary]);

		return $stmt->fetchObject();
	}


	public function delete($primary)
	{
		$stmt = $this->prepare("DELETE FROM {TBL} WHERE {PRI} = :primary LIMIT 1");
		$stmt->execute([":primary" => $primary]);
	}


	public function insert(Array $data)
	{
		$params = array_keys_prepend($data, ':');
		$stmt = $this->prepare("INSERT INTO {TBL} (" . implode(',', array_keys($data)) . ") VALUES(" . implode(',', array_keys($params)) . ")");
		$stmt->execute($params);
	}

}