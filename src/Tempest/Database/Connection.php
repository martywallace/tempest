<?php namespace Tempest\Data;

use Exception;

/**
 * A connection, typically for a database.
 *
 * @author Ascension Web Development
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
	private $host;

	/** @var string */
	private $username;

	/** @var string */
	private $password;

	/** @var string */
	private $resource;

	private function __construct(string $host, string $username, string $password, string $resource) {
		$this->host = $host;
		$this->username = $username;
		$this->password = $password;
		$this->resource = $resource;
	}

	/**
	 * @return string
	 */
	public function getHost(): string {
		return $this->host;
	}

	/**
	 * @return string
	 */
	public function getUsername(): string {
		return $this->username;
	}

	/**
	 * @return string
	 */
	public function getPassword(): string {
		return $this->password;
	}

	/**
	 * @return string
	 */
	public function getResource(): string {
		return $this->resource;
	}

}