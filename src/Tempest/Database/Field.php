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
	 * @return static
	 */
	public static function enum() {
		return new static(static::ENUM);
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
	public function default($value) {
		$this->setDefault($value);

		return $this;
	}

	/**
	 * Adds a primary key index to this field.
	 *
	 * @return $this
	 */
	public function primary() {
		$this->addIndex(Index::PRIMARY);
		return $this;
	}

	/**
	 * Adds a unique index to this field.
	 *
	 * @param string $name Optional index name, used for compound indexes.
	 *
	 * @return $this
	 */
	public function unique($name = null) {
		$this->addIndex(Index::UNIQUE, $name);
		return $this;
	}

	/**
	 * Adds an index to this field.
	 *
	 * @param string $name Optional index name, used for compound indexes.
	 *
	 * @return $this
	 */
	public function index($name = null) {
		$this->addIndex(Index::INDEX, $name);
		return $this;
	}

	/**
	 * Marks this field as auto-incrementing. This also adds a primary key index to this field.
	 *
	 * @return $this
	 */
	public function increments() {
		$this->setAutoIncrement(true);
		$this->addIndex(Index::PRIMARY);

		return $this;
	}

	/**
	 * Marks this field as non-nullable.
	 *
	 * @return $this
	 */
	public function notNullable() {
		$this->setNullable(false);
		return $this;
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