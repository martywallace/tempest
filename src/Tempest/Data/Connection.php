<?php namespace Tempest\Data;

use Exception;

/**
 * A connection, typically for a database.
 *
 * @author Marty Wallace
 */
class Connection {

	/**
	 * Extract login information from a connection string formatted <code>username:password@host/resource</code>. The
	 * password component is optional.
	 *
	 * @param string $connection The connection string.
	 *
	 * @return static
	 *
	 * @throws Exception If the connection string is not valid.
	 */
	public static function fromConnectionString($connection) {
		preg_match('/^(?<username>[^:@]+):?(?<password>.*)?@(?<host>[^\/]+)\/(?<resource>.+)$/', trim($connection), $matches);

		if (!empty($matches)) {
			return new static($matches['host'], $matches['username'], $matches['password'], $matches['resource']);
		} else {
			throw new Exception('The supplied connection string is invalid.');
		}
	}

	/** @var string */
	private $_host;

	/** @var string */
	private $_username;

	/** @var string */
	private $_password;

	/** @var string */
	private $_resource;

	private function __construct($host, $username, $password, $resource) {
		$this->_host = $host;
		$this->_username = $username;
		$this->_password = $password;
		$this->_resource = $resource;
	}

	/**
	 * @return string
	 */
	public function getHost() {
		return $this->_host;
	}

	/**
	 * @return string
	 */
	public function getUsername() {
		return $this->_username;
	}

	/**
	 * @return string
	 */
	public function getPassword() {
		return $this->_password;
	}

	/**
	 * @return string
	 */
	public function getResource() {
		return $this->_resource;
	}

}