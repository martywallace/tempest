<?php namespace Tempest\MySQL;

class Table
{

	private $db;
	private $name;
	private $primary;

	private $indexInfo;

	
	public function __construct(Database $db, $name)
	{
		$this->db = $db;
		$this->name = $name;

		$stmt = $db->prepare("SHOW INDEX FROM $name");
		$stmt->execute();

		$this->indexInfo = $stmt->fetchObject();
		$this->primary = $this->indexInfo->Column_name;
	}


	public function find($primary, Array $fields = null)
	{
		$fields = $fields === null ? '*' : implode(',', $fields);

		$stmt = $this->db->prepare("SELECT $fields FROM $this->name WHERE $this->primary = :primary");
		$stmt->execute([":primary" => $primary]);

		return $stmt->fetchObject();
	}


	public function delete($primary)
	{
		$stmt = $this->db->prepare("DELETE FROM $this->name WHERE $this->primary = :primary LIMIT 1");
		$stmt->execute([":primary" => $primary]);
	}


	public function insert()
	{
		//
	}

}