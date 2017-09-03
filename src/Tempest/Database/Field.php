<?php namespace Tempest\Database;

use Exception;

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

	/**
	 * A field representing an integer value.
	 *
	 * @param int $length The maximum integer length to be stored in this field.
	 * @param bool $unsigned Whether or not the int is unsigned.
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
	 * Field constructor.
	 *
	 * @param string $type
	 */
	protected function __construct($type) {
		$this->setAttr(self::ATTR_TYPE, $type);
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
		$this->setAttr(self::ATTR_DEFAULT, $value);
		return $this;
	}

	/**
	 * Declare that this field is non-nullable.
	 *
	 * @return $this
	 */
	public function notNullable() {
		$this->setAttr(self::ATTR_NULLABLE, false);
		return $this;
	}

	/**
	 * Declare this field as auto-incrementing. This also {@link primary marks the field as part of the PRIMARY key}.
	 *
	 * @return $this
	 */
	public function increments() {
		$this->primary()->setAttr(self::ATTR_AUTO_INCREMENT, true);
		return $this;
	}

	/**
	 * Mark this field as a primary key.
	 *
	 * @return $this
	 */
	public function primary() {
		$this->setAttr(self::ATTR_RPRIMARY_KEY, true);
		return $this;
	}

	/**
	 * Mark this field as a unique key.
	 *
	 * @param string|string[] $name Compound key name, if this is a compound key.
	 *
	 * @return $this
	 */
	public function unique($name = null) {
		$this->addKey(self::KEY_UNIQUE, empty($name) ? true : $name);
		return $this;
	}

	/**
	 * Mark this field as an index.
	 *
	 * @param string|string[] $name Compound key name, if this is a compound key.
	 *
	 * @return $this
	 */
	public function index($name = null) {
		$this->addKey(self::KEY_INDEX, empty($name) ? true : $name);
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