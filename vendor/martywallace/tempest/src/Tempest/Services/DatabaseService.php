<?php namespace Tempest\Services;

use PDO;
use Exception;

/**
 * Provides methods for interacting with a database via PDO.
 *
 * @property-read int $lastInsertId The last insert ID value.
 *
 * @package Tempest\Services
 * @author Marty Wallace
 */
class DatabaseService extends Service {

	/** @var PDO */
	private $_pdo;

	public function __get($prop) {
		if ($prop === 'lastInsertId') return $this->_pdo->lastInsertId();

		return parent::__get($prop);
	}

	protected function setup() {
		$config = app()->config('db');

		if (is_array($config)) {
			if (array_key_exists('host', $config) && array_key_exists('name', $config) &&
				array_key_exists('user', $config) && array_key_exists('pass', $config)) {
				// Set up connection.
				$this->_pdo = new PDO('mysql:host=' . $config['host'] . ';dbname=' . $config['name'], $config['user'], $config['pass']);
			} else {
				throw new Exception('Incomplete connection information provided in database configuration.');
			}
		} else {
			throw new Exception('No database configuration was provided.');
		}
	}

	public function query($query, $params = null) {
		return $this->_pdo->query($query, PDO::ATTR_DEFAULT_FETCH_MODE, $params);
	}

}