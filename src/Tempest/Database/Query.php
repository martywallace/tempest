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
	 *
	 * @return static
	 */
	public static function insert($table, array $data = []) {
		return static::create()->raw('INSERT INTO ' . $table . ' (' . implode(', ', array_keys($data)) . ') VALUES(' . implode(', ', array_fill(0, count($data), '?')) . ')')
			->bind($data);
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
	 * @param mixed|mixed[] $bindings Bindings to provide.
	 *
	 * @return $this
	 */
	public function bind($bindings) {
		if (!is_array($bindings)) $bindings = [$bindings];

		$this->_bindings = array_merge($this->_bindings, array_values($bindings));
		return $this;
	}

	/**
	 * Append raw SQL to the query.
	 *
	 * @param string $query The raw SQL to append.
	 * @param mixed|mixed[] $bindings Values to include in the query.
	 *
	 * @return $this
	 */
	public function raw($query, $bindings = []) {
		$this->_query[] = $query;
		$this->bind($bindings);

		return $this;
	}

	/**
	 * Appends a WHERE [column] = [value] statement to the query for an equal match.
	 *
	 * @param string $column The subject column.
	 * @param mixed $value The value to match.
	 *
	 * @return $this
	 */
	public function where($column, $value) {
		return $this->raw('WHERE ' . $column . ' = ?', $value);
	}

	/**
	 * Appends a WHERE [column] != [value] statement to the query for an equal match.
	 *
	 * @param string $column The subject column.
	 * @param mixed $value The value to match.
	 *
	 * @return $this
	 */
	public function whereNot($column, $value) {
		return $this->raw('WHERE ' . $column . ' != ?', $value);
	}

	/**
	 * Appends a WHERE [column] IN([...values]) statement to the query for an equal match.
	 *
	 * @param string $column The subject column.
	 * @param array $values The value to match.
	 *
	 * @return $this
	 */
	public function whereIn($column, array $values) {
		return $this->raw('WHERE ' . $column . ' IN(' . array_fill(0, count($values), '?') . ')', $values);
	}

	/**
	 * Appends a WHERE [column] NOT IN([...values]) statement to the query for an equal match.
	 *
	 * @param string $column The subject column.
	 * @param array $values The value to match.
	 *
	 * @return $this
	 */
	public function whereNotIn($column, array $values) {
		return $this->raw('WHERE ' . $column . ' NOT IN(' . array_fill(0, count($values), '?') . ')', $values);
	}

	/**
	 * Appends a WHERE [column] < [value] statement to the query for a less than match.
	 *
	 * @param string $column The subject column.
	 * @param mixed $value The value to match.
	 *
	 * @return $this
	 */
	public function whereLess($column, $value) {
		return $this->raw('WHERE ' . $column . ' < ?', $value);
	}

	/**
	 * Appends a WHERE [column] <= [value] statement to the query for a less than match.
	 *
	 * @param string $column The subject column.
	 * @param mixed $value The value to match.
	 *
	 * @return $this
	 */
	public function whereLessOrEqual($column, $value) {
		return $this->raw('WHERE ' . $column . ' <= ?', $value);
	}

	/**
	 * Appends a WHERE [column] > [value] statement to the query for a greater than match.
	 *
	 * @param string $column The subject column.
	 * @param mixed $value The value to match.
	 *
	 * @return $this
	 */
	public function whereGreater($column, $value) {
		return $this->raw('WHERE ' . $column . ' > ?', $value);
	}

	/**
	 * Appends a WHERE [column] >= [value] statement to the query for a less than match.
	 *
	 * @param string $column The subject column.
	 * @param mixed $value The value to match.
	 *
	 * @return $this
	 */
	public function whereGreaterOrEqual($column, $value) {
		return $this->raw('WHERE ' . $column . ' >= ?', $value);
	}

	/**
	 * Appends a WHERE [column] LIKE [value] statement to the query.
	 *
	 * @param string $column The subject column.
	 * @param mixed $value The value to match.
	 *
	 * @return $this
	 */
	public function whereLike($column, $value) {
		return $this->raw('WHERE ' . $column . ' LIKE ?', $value);
	}

	/**
	 * Appends an INNER JOIN [table] ON [left] = [right] statement to the query.
	 *
	 * @param string $table The table to join.
	 * @param string $left The left side of the ON statement.
	 * @param string $right The right side of the ON statement.
	 *
	 * @return $this
	 */
	public function innerJoin($table, $left, $right) {
		return $this->raw('INNER JOIN ' . $table . ' ON ' . $left . ' = ' . $right);
	}

	/**
	 * Appends a LEFT JOIN [table] ON [left] = [right] statement to the query.
	 *
	 * @param string $table The table to join.
	 * @param string $left The left side of the ON statement.
	 * @param string $right The right side of the ON statement.
	 *
	 * @return $this
	 */
	public function leftJoin($table, $left, $right) {
		return $this->raw('LEFT JOIN ' . $table . ' ON ' . $left . ' = ' . $right);
	}

	/**
	 * Appends a RIGHT JOIN [table] ON [left] = [right] statement to the query.
	 *
	 * @param string $table The table to join.
	 * @param string $left The left side of the ON statement.
	 * @param string $right The right side of the ON statement.
	 *
	 * @return $this
	 */
	public function rightJoin($table, $left, $right) {
		return $this->raw('RIGHT JOIN ' . $table . ' ON ' . $left . ' = ' . $right);
	}

	/**
	 * Appends an AND [column] = [value] statement to the query.
	 *
	 * @param string $column The subject field.
	 * @param mixed $value The value.
	 *
	 * @return $this
	 */
	public function and($column, $value) {
		return $this->raw('AND ' . $column . ' = ?', $value);
	}

	/**
	 * Appends an AND [column] != [value] statement to the query.
	 *
	 * @param string $column The subject field.
	 * @param mixed $value The value.
	 *
	 * @return $this
	 */
	public function andNot($column, $value) {
		return $this->raw('AND ' . $column . ' != ?', $value);
	}

	/**
	 * Appends an AND [column] IN([...values]) statement to the query for an equal match.
	 *
	 * @param string $column The subject column.
	 * @param array $values The value to match.
	 *
	 * @return $this
	 */
	public function andIn($column, array $values) {
		return $this->raw('AND ' . $column . ' IN(' . array_fill(0, count($values), '?') . ')', $values);
	}

	/**
	 * Appends an AND [column] NOT IN([...values]) statement to the query for an equal match.
	 *
	 * @param string $column The subject column.
	 * @param array $values The value to match.
	 *
	 * @return $this
	 */
	public function andNotIn($column, array $values) {
		return $this->raw('AND ' . $column . ' NOT IN(' . array_fill(0, count($values), '?') . ')', $values);
	}

	/**
	 * Appends an AND [column] < [value] statement to the query.
	 *
	 * @param string $column The subject field.
	 * @param mixed $value The value.
	 *
	 * @return $this
	 */
	public function andLess($column, $value) {
		return $this->raw('AND ' . $column . ' < ?', $value);
	}

	/**
	 * Appends an AND [column] <= [value] statement to the query.
	 *
	 * @param string $column The subject field.
	 * @param mixed $value The value.
	 *
	 * @return $this
	 */
	public function andLessOrEqual($column, $value) {
		return $this->raw('AND ' . $column . ' <= ?', $value);
	}

	/**
	 * Appends an AND [column] > [value] statement to the query.
	 *
	 * @param string $column The subject field.
	 * @param mixed $value The value.
	 *
	 * @return $this
	 */
	public function andGreater($column, $value) {
		return $this->raw('AND ' . $column . ' > ?', $value);
	}

	/**
	 * Appends an AND [column] >= [value] statement to the query.
	 *
	 * @param string $column The subject field.
	 * @param mixed $value The value.
	 *
	 * @return $this
	 */
	public function andGreaterOrEqual($column, $value) {
		return $this->raw('AND ' . $column . ' >= ?', $value);
	}

	/**
	 * Appends an AND [column] LIKE [value] statement to the query.
	 *
	 * @param string $column The subject column.
	 * @param mixed $value The value to match.
	 *
	 * @return $this
	 */
	public function andLike($column, $value) {
		return $this->raw('AND ' . $column . ' LIKE ?', $value);
	}

	/**
	 * Appends an OR [column] = [value] statement to the query.
	 *
	 * @param string $column The subject field.
	 * @param mixed $value The value.
	 *
	 * @return $this
	 */
	public function or($column, $value) {
		return $this->raw('OR ' . $column . ' = ?', $value);
	}

	/**
	 * Appends an OR [column] != [value] statement to the query.
	 *
	 * @param string $column The subject field.
	 * @param mixed $value The value.
	 *
	 * @return $this
	 */
	public function orNot($column, $value) {
		return $this->raw('OR ' . $column . ' != ?', $value);
	}

	/**
	 * Appends an OR [column] IN([...values]) statement to the query for an equal match.
	 *
	 * @param string $column The subject column.
	 * @param array $values The value to match.
	 *
	 * @return $this
	 */
	public function orIn($column, array $values) {
		return $this->raw('OR ' . $column . ' IN(' . array_fill(0, count($values), '?') . ')', $values);
	}

	/**
	 * Appends an OR [column] NOT IN([...values]) statement to the query for an equal match.
	 *
	 * @param string $column The subject column.
	 * @param array $values The value to match.
	 *
	 * @return $this
	 */
	public function orNotIn($column, array $values) {
		return $this->raw('OR ' . $column . ' NOT IN(' . array_fill(0, count($values), '?') . ')', $values);
	}

	/**
	 * Appends an OR [column] < [value] statement to the query.
	 *
	 * @param string $column The subject field.
	 * @param mixed $value The value.
	 *
	 * @return $this
	 */
	public function orLess($column, $value) {
		return $this->raw('OR ' . $column . ' < ?', $value);
	}

	/**
	 * Appends an OR [column] <= [value] statement to the query.
	 *
	 * @param string $column The subject field.
	 * @param mixed $value The value.
	 *
	 * @return $this
	 */
	public function orLessOrEqual($column, $value) {
		return $this->raw('OR ' . $column . ' <= ?', $value);
	}

	/**
	 * Appends an OR [column] > [value] statement to the query.
	 *
	 * @param string $column The subject field.
	 * @param mixed $value The value.
	 *
	 * @return $this
	 */
	public function orGreater($column, $value) {
		return $this->raw('OR ' . $column . ' > ?', $value);
	}

	/**
	 * Appends an OR [column] >= [value] statement to the query.
	 *
	 * @param string $column The subject field.
	 * @param mixed $value The value.
	 *
	 * @return $this
	 */
	public function orGreaterOrEqual($column, $value) {
		return $this->raw('OR ' . $column . ' >= ?', $value);
	}

	/**
	 * Appends an OR [column] LIKE [value] statement to the query.
	 *
	 * @param string $column The subject column.
	 * @param mixed $value The value to match.
	 *
	 * @return $this
	 */
	public function orLike($column, $value) {
		return $this->raw('OR ' . $column . ' LIKE ?', $value);
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
	 * Append a GROUP BY [column] statement to the query.
	 *
	 * @param string $column The column to group by.
	 *
	 * @return $this
	 */
	public function groupBy($column) {
		return $this->raw('GROUP BY ' .$column);
	}

	/**
	 * Appends an ON DUPLICATE KEY UPDATE statement to the query.
	 *
	 * @param array $data The fields to update if a duplicate was detected in the previous {@link insert INSERT}.
	 *
	 * @return $this
	 */
	public function onDuplicateKeyUpdate(array $data) {
		$pairs = array_map(function($field) { return $field . ' = ?'; }, array_keys($data));
		return $this->raw('ON DUPLICATE KEY UPDATE ' . implode(', ', $pairs))->bind($data);
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