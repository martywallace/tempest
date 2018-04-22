<?php namespace Tempest\Services;

use PDO;
use PDOStatement;
use Exception;
use Tempest\App;
use Tempest\Data\Connection;
use Tempest\Database\Row;

/**
 * Provides access to the application database.
 *
 * @author Ascension Web Development
 */
class DatabaseService implements Service {

	/** @var PDO */
	private $_pdo;

	/** @var string[] */
	private $_prepared;

	public function __construct() {
		$conn = Connection::fromConnectionString(App::get()->config('db'));

		$this->_pdo = new PDO('mysql:host=' . $conn->getHost() . ';dbname=' . $conn->getResource(), $conn->getUsername(), $conn->getPassword());

		$this->_pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
		$this->_pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	}

	/**
	 * Get the internal PDO instance.
	 *
	 * @return PDO
	 */
	public function getPdo() {
		return $this->_pdo;
	}

	/**
	 * Get the last insert ID.
	 *
	 * @return string
	 */
	public function getLastInsertId() {
		return $this->_pdo->lastInsertId();
	}

	/**
	 * Return all prepared statements, assuming the application is in {@link App::dev development mode}.
	 *
	 * @return string[]
	 */
	public function getPrepared() {
		return $this->_prepared;
	}

	/**
	 * Prepares a PDOStatement.
	 *
	 * @param string $query The query to prepare.
	 *
	 * @return PDOStatement
	 */
	public function prepare($query) {
		if (App::get()->dev) {
			$this->_prepared[] = $query;
		}

		return $this->_pdo->prepare($query);
	}

	/**
	 * Prepare and execute a query, returning the PDOStatement that is created when preparing the query.
	 *
	 * @param string $query The query to execute.
	 * @param array $bindings Optional parameters to bind to the query.
	 *
	 * @return PDOStatement
	 */
	public function query($query, array $bindings = []) {
		$stmt = $this->prepare($query);
		$stmt->execute($bindings);

		return $stmt;
	}

	/**
	 * Returns the first row provided by executing a query.
	 *
	 * @param string $query The query to execute.
	 * @param array $bindings Parameters to bind to the query.
	 *
	 * @return Row
	 */
	public function one($query, array $bindings = []) {
		$rows = $this->all($query, $bindings);
		return count($rows) > 0 ? $rows[0] : null;
	}

	/**
	 * Returns all rows provided by executing a query.
	 *
	 * @param string $query The query to execute.
	 * @param array $bindings Parameters to bind to the query.
	 *
	 * @return Row[]
	 */
	public function all($query, array $bindings = []) {
		$stmt = $this->query($query, $bindings);
		return $stmt->fetchAll(PDO::FETCH_CLASS, Row::class);
	}

	/**
	 * Returns the first value in the first column returned from executing a query.
	 *
	 * @param string $query The query to execute.
	 * @param array $bindings Parameters to bind to the query.
	 * @param mixed $fallback A fallback value to use if no results were returned by the query.
	 *
	 * @return mixed
	 *
	 * @throws Exception If the internal PDOStatement returns any errors, they are thrown as an exception.
	 */
	public function prop($query, array $bindings = [], $fallback = null) {
		$result = $this->query($query, $bindings)->fetch(PDO::FETCH_NUM);
		return empty($result) ? $fallback : $result[0];
	}

}