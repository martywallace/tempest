<?php namespace Tempest\Services;

use PDO;
use PDOStatement;
use Exception;

/**
 * Provides methods for interacting with a database via PDO.
 *
 * @property-read int $lastInsertId The last insert ID value.
 * @property-read array $errors Any errors provided by PDO::errorInfo() internally.
 * @property-read string[] $queries An array of all previously executed queries for this request. This array does not
 * populate unless the application is in development mode.
 *
 * @package Tempest\Services
 * @author Marty Wallace
 */
class DatabaseService extends Service {

	/** @var PDO */
	private $_pdo;

	/** @var string[] */
	private $_queries = array();

	public function __get($prop) {
		if ($prop === 'lastInsertId') return $this->_pdo->lastInsertId();
		if ($prop === 'errors') return $this->_pdo->errorInfo();
		if ($prop === 'queries') return $this->_queries;

		return null;
	}

	protected function setup() {
		$config = app()->config('db');

		if (is_array($config)) {
			if (array_key_exists('host', $config) && array_key_exists('name', $config) &&
				array_key_exists('user', $config) && array_key_exists('pass', $config)) {
				// Set up connection.
				$this->_pdo = new PDO('mysql:host=' . $config['host'] . ';dbname=' . $config['name'], $config['user'], $config['pass']);
			} else {
				throw new Exception('Incomplete database connection information provided in app configuration.');
			}
		} else {
			throw new Exception('No database configuration was provided.');
		}
	}

	/**
	 * Prepares a PDOStatement.
	 *
	 * @param string $query The query to prepare.
	 *
	 * @return PDOStatement
	 */
	public function prepare($query) {
		if (app()->dev) {
			$this->_queries[] = $query;
		}

		return $this->_pdo->prepare($query);
	}

	/**
	 * Prepare and execute a query, returning the PDOStatement that is created when preparing the query.
	 *
	 * @param string $query The query to execute.
	 * @param array|null $params Parameters to bind to the query.
	 *
	 * @return PDOStatement
	 *
	 * @throws Exception If the PDOStatement returns any errors, they are thrown as an exception.
	 */
	public function query($query, array $params = null) {
		$stmt = $this->prepare($query);
		$stmt->execute($params);

		if ($stmt->errorCode() !== '00000') {
			$err = $stmt->errorInfo();
			throw new Exception($err[0] . ': ' . $err[2]);
		}

		return $stmt;
	}

	/**
	 * Returns all rows provided by executing a query.
	 *
	 * @param string $query The query to execute.
	 * @param array|null $params Parameters to bind to the query.
	 * @param string|null $class The name of a class to optionally create and inject the returned values into.
	 *
	 * @return array
	 *
	 * @throws Exception If the internal PDOStatement returns any errors, they are thrown as an exception.
	 * @throws Exception If the provided class does not exist.
	 */
	public function all($query, array $params = null, $class = null) {
		$stmt = $this->query($query, $params);

		if (!empty($class)) {
			$class = '\\' . ltrim($class, '\\');

			if (class_exists($class)) {
				return $stmt->fetchAll(PDO::FETCH_CLASS, $class);
			} else {
				throw new Exception('Class "' . $class . '" does not exist.');
			}
		} else {
			return $stmt->fetchAll(PDO::FETCH_OBJ);
		}
	}

	/**
	 * Returns the first row provided by executing a query.
	 *
	 * @param string $query The query to execute.
	 * @param array|null $params Parameters to bind to the query.
	 * @param string|null $class The name of a class to optionally create and inject the returned values into.
	 *
	 * @return mixed
	 *
	 * @throws Exception If the internal PDOStatement returns any errors, they are thrown as an exception.
	 * @throws Exception If the provided class does not exist.
	 */
	public function one($query, array $params = null, $class = null) {
		$all = $this->all($query, $params, $class);

		if (!empty($all)) {
			return $all[0];
		}

		return null;
	}

	/**
	 * Returns the first value in the first column returned from executing a query.
	 *
	 * @param string $query The query to execute.
	 * @param array|null $params Parameters to bind to the query.
	 * @param mixed $fallback A fallback value to use if no results were returned by the query.
	 *
	 * @return mixed
	 *
	 * @throws Exception If the internal PDOStatement returns any errors, they are thrown as an exception.
	 */
	public function prop($query, array $params = null, $fallback = null) {
		$result = $this->query($query, $params)->fetch(PDO::FETCH_NUM);

		if (!empty($result)) {
			return $result[0];
		}

		return $fallback;
	}

}