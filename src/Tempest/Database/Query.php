<?php namespace Tempest\Database;

use Exception;
use Closure;
use Tempest\App;

/**
 * A query to perform on the database.
 *
 * @author Marty Wallace
 */
class Query {

	const ORDER_ASC = 'ASC';
	const ORDER_DESC = 'DESC';

	/**
	 * Creates an empty query.
	 *
	 * @return static
	 */
	public static function create() {
		return new static();
	}

	/**
	 * Creates a SELECT query.
	 *
	 * @param string $table The table to SELECT from.
	 * @param array $fields The fields to select.
	 *
	 * @return static
	 */
	public static function select($table, array $fields = ['*']) {
		return static::create()->raw('SELECT ' . implode(', ', $fields) . ' FROM ' . $table);
	}

	/**
	 * Creates a DELETE query.
	 *
	 * @param string $table The table to DELETE from.
	 *
	 * @return static
	 */
	public static function delete($table) {
		return static::create()->raw('DELETE FROM ' . $table);
	}

	/**
	 * Creates an INSERT INTO query.
	 *
	 * @param string $table The table to insert data into.
	 * @param array $data The data to insert.
	 * @param array $updatable If provided, includes an ON DUPLICATE KEY UPDATE statement for the provided columns.
	 *
	 * @return static
	 */
	public static function insert($table, array $data = [], array $updatable = []) {
		$fields = array_keys($data);
		$placeholders = array_map(function($field) { return ':' . $field; }, $fields);

		$query = static::create()->raw('INSERT INTO ' . $table . '(' . implode(', ', $fields) . ') VALUES(' . implode(', ', $placeholders) . ')');

		if (!empty($updatable)) {
			$pairs = array_map(function($field) {
				return $field . ' = :' . $field;
			}, array_intersect($updatable, $fields));

			$query = $query->raw('ON DUPLICATE KEY UPDATE ' . implode(', ', $pairs));
		}

		$query->bind(array_combine($placeholders, $data));

		return $query;
	}

	/** @var string[] */
	private $_query = [];

	/** @var array */
	private $_bindings = [];

	/** @var string */
	private $_produces = null;

	private function __construct() { }

	/**
	 * Get the current query to perform.
	 *
	 * @return string
	 */
	public function getQuery() {
		return implode(' ', $this->_query);
	}

	/**
	 * Get the current bound variables.
	 *
	 * @return array
	 */
	public function getBindings() {
		return $this->_bindings;
	}

	/**
	 * Get the model type that this query produces, if one was set.
	 *
	 * @return string
	 */
	public function getProduces() {
		return $this->_produces;
	}

	/**
	 * Define a model that this query should produce.
	 *
	 * @param string $model The model to produce.
	 *
	 * @return $this
	 */
	public function produces($model) {
		$this->_produces = $model;
		return $this;
	}

	/**
	 * Add value bindings.
	 *
	 * @param array $bindings Bindings to provide.
	 *
	 * @return $this
	 */
	public function bind(array $bindings) {
		$this->_bindings = array_merge($this->_bindings, $bindings);
		return $this;
	}

	/**
	 * Append raw SQL to the query.
	 *
	 * @param string $query The raw SQL to append.
	 * @param array $bindings Values to include in the query.
	 *
	 * @return $this
	 */
	public function raw($query, array $bindings = []) {
		$this->_query[] = $query;
		$this->bind($bindings);

		return $this;
	}

	/**
	 * Appends a WHERE statement to the query.
	 *
	 * @param string $column The subject column.
	 * @param mixed $value The value to match.
	 *
	 * @return $this
	 */
	public function where($column, $value) {
		return $this->raw('WHERE ' . $column . ' = ?', [$value]);
	}

	/**
	 * Appends an ORDER BY statement to the query.
	 *
	 * @param string $column The subject column.
	 * @param string $order The sort order.
	 *
	 * @return $this
	 *
	 * @throws Exception If the sort order is not ASC or DESC.
	 */
	public function order($column, $order = 'ASC') {
		$order = strtoupper($order);

		if (!in_array($order, [self::ORDER_ASC, self::ORDER_DESC])) {
			throw new Exception('Unknown sort order "' . $order . '".');
		}

		return $this->raw('ORDER BY ' . $column . ' ' . $order);
	}

	/**
	 * Append a LIMIT statement to the query.
	 *
	 * @param int $limit The limit.
	 * @param int $offset An optional offset.
	 *
	 * @return $this
	 */
	public function limit($limit, $offset = 0) {
		return $this->raw('LIMIT ?, ?', [$offset, $limit]);
	}

	/**
	 * Execute the query and return all models or rows it fetches. If this query has been set to
	 * {@link produces() produce} a specific type of model, instances of that model are returned.
	 *
	 * @return Row[]|Model[]
	 */
	public function all() {
		$rows = App::get()->db->all($this->getQuery(), $this->getBindings());

		return empty($this->getProduces()) ? $rows : Closure::fromCallable([$this->getProduces(), 'from'])($rows);
	}

	/**
	 * Execute the query and return the first model or row it fetches. If this query has been set to
	 * {@link produces() produce} a specific type of model, an instance of that model is returned.
	 *
	 * @return Row|Model
	 */
	public function first() {
		$all = $this->all();
		return empty($all) ? null : $all[0];
	}

	/**
	 * Execute the query.
	 */
	public function execute() {
		App::get()->db->query($this->getQuery(), $this->getBindings());
	}

}