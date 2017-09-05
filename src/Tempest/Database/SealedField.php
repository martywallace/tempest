<?php namespace Tempest\Database;

/**
 * A real-only field declaration.
 *
 * @author Marty Wallace
 */
class SealedField {

	/** @var string */
	private $_name;

	/** @var mixed */
	private $_type;

	/** @var bool */
	private $_autoIncrement = false;

	/** @var mixed */
	private $_default = null;

	/** @var bool */
	private $_nullable = true;

	/** @var Index[] */
	private $_indexes = [];

	protected function __construct($name, Field $field) {
		$this->_name = $name;
		$this->_type = $field->getType();
		$this->_autoIncrement = $field->getAutoIncrement();
		$this->_default = $field->getDefault();
		$this->_nullable = $field->getNullable();
		$this->_indexes = $field->getIndexes();
	}

	/**
	 * Converts a value to a storable, raw value for MySQL.
	 *
	 * @param mixed $value The value to convert.
	 *
	 * @return mixed
	 */
	public function toRaw($value) {
		if (!$this->isNull($value)) {
			return $value;
		}

		return null;
	}

	/**
	 * Converts a value to a refined, usable value for application development.
	 *
	 * @param mixed $value The value to convert.
	 *
	 * @return mixed
	 */
	public function toRefined($value) {
		if (!$this->isNull($value)) {
			return $value;
		}

		return null;
	}

	/**
	 * Determine whether a value should be considered NULL.
	 *
	 * @param mixed $value The value to check.
	 *
	 * @return bool
	 */
	public function isNull($value) {
		if ($value === null) {
			return true;
		}

		switch ($this->_type) {
			default:
				return false;
				break;

			case Field::INT:
			case Field::DECIMAL:
				return empty($value)
					&& $value !== 0
					&& $value !== 0.0
					&& $value !== '0'
					&& $value !== '0.0';
				break;

			case Field::STRING:
			case Field::TEXT:
			case Field::JSON:
			case Field::ENUM:
				return empty($value)
					&& $value !== ''
					&& $value !== 0
					&& $value !== '0';
				break;

			case Field::DATETIME:
				return empty($value);
				break;

			case Field::BOOL:
				return empty($value)
					&& $value !== false
					&& $value !== 0
					&& $value !== 0.0
					&& $value !== '0'
					&& $value !== '0.0';
				break;
		}
	}

	/**
	 * Retrieve the name of this field as provided when {@link Model::fields declaring the available fields} for a
	 * model.
	 *
	 * @return string
	 */
	public function getName() {
		return $this->_name;
	}

	/**
	 * Get the field type.
	 *
	 * @return string
	 */
	public function getType() {
		return $this->_type;
	}

	/**
	 * Set the field type.
	 *
	 * @param string $value The type.
	 */
	protected function setType($value) {
		$this->_type = $value;
	}

	/**
	 * Retrieve the default value for this field.
	 *
	 * @return mixed
	 */
	public function getDefault() {
		return $this->_default;
	}

	/**
	 * Sets the default value for this field.
	 *
	 * @param mixed $value The value.
	 */
	protected function setDefault($value) {
		$this->_default = $value;
	}

	/**
	 * Whether or not this field auto-increments.
	 *
	 * @return bool
	 */
	public function getAutoIncrement() {
		return $this->_autoIncrement;
	}

	/**
	 * Sets whether this field auto-increments or not.
	 *
	 * @param bool $value The value.
	 */
	protected function setAutoIncrement($value) {
		$this->_autoIncrement = $value;
	}

	/**
	 * Whether or not this field is nullable.
	 *
	 * @return bool
	 */
	public function getNullable() {
		return $this->_nullable;
	}

	/**
	 * Sets whether or not this field is nullable.
	 *
	 * @param bool $value The value.
	 */
	protected function setNullable($value) {
		$this->_nullable = $value;
	}

	/**
	 * Retrieve all indexes added to this field.
	 *
	 * @return Index[]
	 */
	public function getIndexes() {
		return $this->_indexes;
	}

	/**
	 * Adds a new index to this field.
	 *
	 * @param string $type The index type.
	 * @param string $name The index name.
	 */
	protected function addIndex($type, $name = null) {
		if ($type === Index::PRIMARY && $this->hasPrimaryIndex()) {
			// Only one primary key.
			return;
		}

		$this->_indexes[] = new Index($type, $name);
	}

	/**
	 * Sets the indexes added to this field.
	 *
	 * @param array $indexes The indexes to set.
	 */
	protected function setIndexes(array $indexes) {
		$this->_indexes = $indexes;
	}

	/**
	 * Whether or not this field has a primary index.
	 *
	 * @return bool
	 */
	public function hasPrimaryIndex() {
		foreach ($this->_indexes as $index) {
			if ($index->getType() === Index::PRIMARY) return true;
		}

		return false;
	}

	/**
	 * Whether or not this field has a unique index. This includes both PRIMARY and UNIQUE indexes.
	 *
	 * @return bool
	 */
	public function hasUniqueIndex() {
		foreach ($this->_indexes as $index) {
			if ($index->getType() === Index::PRIMARY || $index->getType() === Index::UNIQUE) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Whether or not this field has any indexes.
	 *
	 * @return bool
	 */
	public function hasIndex() {
		return count($this->_indexes) > 0;
	}

}