<?php namespace Tempest\Database;

use Tempest\App;
use PDOStatement;

/**
 * An SQL query to perform.
 *
 * @author Marty Wallace
 */
class Query {

	/**
	 * A raw query.
	 *
	 * @param $query
	 *
	 * @return static
	 */
	public static function raw($query) {
		return new static($query);
	}

	/** @var string */
	private $_query = '';

	/** @var array */
	private $_args = [];

	private function __construct($query) {
		$this->_query = $query;
	}

	/**
	 * Execute the query and get the resulting {@link Row rows}.
	 *
	 * @return Row[]
	 */
	public function get() {
		return App::get()->db->all($this->_query, $this->_args);
	}

}