<?php namespace Tempest\MySQL;

use PDO;
use PDOStatement;
use Tempest\Services\IService;


class Database extends PDO implements IService
{

	public function __construct()
	{
		$connection = tempest()->config('db');

		if($connection !== null)
			parent::__construct("mysql:host={$connection['host']};dbname={$connection['dbname']}", $connection["user"], $connection["pass"]);
	}


	public function insert($table, Array $params)
	{
		$p2 = array_keys_prepend($params, ':');
		$stmt = $this->prepare("INSERT INTO {$table} (" . implode(',', array_keys($params)) . ") VALUES(" . implode(',', array_keys($p2)) . ")");
		$this->execute($stmt, $params);

		return $stmt;
	}


	public function all($query, Array $params = null, $model = null)
	{
		$stmt = $this->prepare($query);
		$this->execute($stmt, $params);

		$result = $stmt->fetchAll(PDO::FETCH_CLASS, $model === null ? 'stdclass' : $model);
		return $result === false ? array() : $result;
	}


	public function first($query, Array $params = null, $model = null)
	{
		$result = $this->all($query, $params, $model);
		return count($result) > 0 ? $result[0] : null;
	}


	public function assoc($query, Array $params = null)
	{
		$stmt = $this->prepare($query);
		$this->execute($stmt, $params);

		return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}


	public function prop($query, Array $params = null)
	{
		$stmt = $this->prepare($query);
		$this->execute($stmt, $params);

		$result = $stmt->fetch(PDO::FETCH_NUM);
		return $result[0];
	}


	public function execute(PDOStatement $stmt, Array $params = null)
	{
		if($params === null) $stmt->execute();
		else $stmt->execute($params);

		$error = $stmt->errorInfo();

		if($error[0] !== "00000")
		{
			// Append errors to application error log.
			trigger_error($error[2]);
		}


		return $stmt;
	}


	/**
	 * Whether or not this service is accessible within Twig templates.
	 *
	 * @return bool
	 */
	public function isTwigAccessible() { return false; }

}