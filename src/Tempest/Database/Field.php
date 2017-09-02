<?php namespace Tempest\Database;

use Closure;

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
	public static function int($length = 10, $unsigned = true) {
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
		parent::__construct($this);
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
	 * Declare this field as auto-incrementing.
	 *
	 * @return $this
	 */
	public function increments() {
		$this->setAttr(self::ATTR_AUTO_INCREMENT, true);
		return $this;
	}

	/**
	 * Mark this column as a primary key.
	 *
	 * @param string $compound Compound key name, if this is a compund key.
	 *
	 * @return $this
	 */
	public function primary($compound = null) {
		$this->setAttr(self::ATTR_PRIMARY, empty($compound) ? true : $compound);
		return $this;
	}

	public function unique() {
		//
	}

	public function index() {
		//
	}

	/**
	 * Return a read-only version of this field.
	 *
	 * @internal
	 *
	 * @return SealedField
	 */
	public function seal() {
		return new SealedField($this);
	}

}