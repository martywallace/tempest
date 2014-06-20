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
	}


	public function find($primary, Array $fields = null)
	{
		$fields = $fields === null ? '*' : implode(',', $fields);

		$stmt = $this->db->prepare("SELECT $fields FROM $this->name WHERE $this->primary = :primary");
		$stmt->execute([":primary" => $primary]);

		return $stmt->fetchObject();
	}

}