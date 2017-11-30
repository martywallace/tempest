<?php namespace Tempest\Database;

/**
 * A field for a model.
 *
 * @author Marty Wallace
 */
class Field extends SealedField {

	const INT = 'int';
	const STRING = 'string';
	const DATETIME = 'datetime';
	const BOOL = 'bool';
	const TEXT = 'text';
	const DECIMAL = 'decimal';
	const JSON = 'json';
	const ENUM = 'enum';

	/**
	 * A field representing an integer value.
	 *
	 * @return static
	 */
	public static function int() {
		return new static(static::INT);
	}

	/**
	 * A field representing a string value.
	 *
	 * @return static
	 */
	public static function string() {
		return new static(static::STRING);
	}

	/**
	 * A field representing date and time.
	 *
	 * @return static
	 */
	public static function dateTime() {
		return new static(static::DATETIME);
	}

	/**
	 * A field representing a boolean value.
	 *
	 * @return static
	 */
	public static function bool() {
		return new static(static::BOOL);
	}

	/**
	 * A field representing a text value.
	 *
	 * @return static
	 */
	public static function text() {
		return new static(static::TEXT);
	}

	/**
	 * A field representing a decimal value.
	 *
	 * @return static
	 */
	public static function decimal() {
		return new static(static::DECIMAL);
	}

	/**
	 * A field representing a JSON value.
	 *
	 * @return static
	 */
	public static function json() {
		return new static(static::JSON);
	}

	/**
	 * A field representing an enum value.
	 *
	 * @param string[] The enum set.
	 *
	 * @return static
	 */
	public static function enum(array $set) {
		$field = new static(static::ENUM);
		$field->setEnumerable($set);

		return $field;
	}

	/**
	 * Field constructor.
	 *
	 * @param string $type
	 */
	protected function __construct($type) {
		$this->setType($type);

		parent::__construct($this->getName(), $this);
	}

	/**
	 * Sets the default value for this field.
	 *
	 * @param mixed $value The default value.
	 *
	 * @return $this
	 */
	public function setDefault($value) {
		return $this->setDefaultInternally($value);
	}

	/**
	 * Adds a primary key index to this field.
	 *
	 * @return $this
	 */
	public function setPrimary() {
		return $this->addIndexInternally(Index::PRIMARY)->setNotNullable();
	}

	/**
	 * Adds a unique index to this field.
	 *
	 * @param string $name Optional index name, used for compound indexes.
	 *
	 * @return $this
	 */
	public function addUniqueKey($name = null) {
		return $this->addIndexInternally(Index::UNIQUE, $name);
	}

	/**
	 * Adds an index to this field.
	 *
	 * @param string $name Optional index name, used for compound indexes.
	 *
	 * @return $this
	 */
	public function addIndex($name = null) {
		return $this->addIndexInternally(Index::INDEX, $name);
	}

	/**
	 * Marks this field as auto-incrementing. This also adds a primary key index to this field.
	 *
	 * @return $this
	 */
	public function setAutoIncrements() {
		return $this->setAutoIncrementsInternally()->setPrimary();
	}

	/**
	 * Marks this field as non-nullable.
	 *
	 * @return $this
	 */
	public function setNotNullable() {
		return $this->setNullableInternally(false);
	}

	/**
	 * Return a read-only version of this field.
	 *
	 * @internal
	 *
	 * @param string $name The name of the field.
	 *
	 * @return SealedField
	 */
	public function seal($name) {
		return new SealedField($name, $this);
	}

}