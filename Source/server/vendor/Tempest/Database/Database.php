<?php namespace Tempest\Database;

use PDO;
use PDOStatement;


class Database
{

	private static $pdo;
	private static $errors = array();


	public static function setup($host, $dbname, $user, $pass)
	{
		self::$pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
	}


	public static function error(PDOStatement $stmt, $message = "")
	{
		$base = $stmt->errorInfo();
		self::$errors[] = array("sql_code" => $base[0], "driver_code" => $base[1], "driver_message" => $base[2], "app_message" => $message);
	}


	public static function prepare($query)
	{
		return self::$pdo->prepare($query);
	}


	public function fetchObject($query, $params = null, $class = 'stdClass')
	{
		$stmt = self::$pdo->prepare($query);
		
		if($params === null) $stmt->execute();
		else $stmt->execute($params);

		return $stmt->fetchObject($class);
	}


	public static function getPDO(){ return self::$pdo; }
	public static function lastInsertId(){ return self::$pdo->lastInsertId(); }
	public static function getErrors(){ return self::$errors; }

}