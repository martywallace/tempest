<?php namespace Tempest\Database;

use JsonSerializable;

/**
 * A single row from a database query.
 *
 * @author Marty Wallace
 */
class Row implements JsonSerializable {

	/** @var array */
	private $_data = [];

	public function __construct() {
		foreach ($this as $column => $value) {
			if (!in_array($column, ['_data'])) {
				$this->_data[$column] = $value;
			}
		}
	}

	public function __get($prop) {
		return $this->getValue($prop);
	}

	public function __isset($prop) {
		return $this->hasColumn($prop);
	}

	/**
	 * Get the column names attached to this row.
	 *
	 * @return string[]
	 */
	public function getColumns() {
		return array_keys($this->_data);
	}

	/**
	 * Determine whether this row contains a specified column.
	 *
	 * @param string $column The column to check for.
	 *
	 * @return bool
	 */
	public function hasColumn($column) {
		return array_key_exists($column, $this->_data);
	}

	/**
	 * Get the values attached to this row.
	 *
	 * @return string[]
	 */
	public function getValues() {
		return $this->_data;
	}

	/**
	 * Get a value from the row.
	 *
	 * @param string $column The column name.
	 * @param mixed $fallback A fallback value to provide if the column does not exist.
	 *
	 * @return mixed
	 */
	public function getValue($column, $fallback = null) {
		return $this->hasColumn($column) ? $this->_data[$column] : $fallback;
	}

	public function jsonSerialize() {
		return $this->_data;
	}

}