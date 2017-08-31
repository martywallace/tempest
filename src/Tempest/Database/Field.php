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
	 * Field constructor.
	 *
	 * @param string $type
	 */
	protected function __construct($type) {
		$this->type = $type;

		parent::__construct($this);
	}

	/**
	 * Sets the default value for this field.
	 *
	 * @param mixed $value The default value.
	 *
	 * @return $this
	 */
	public function setDefault($value) {
		$this->default = $value;
		return $this;
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