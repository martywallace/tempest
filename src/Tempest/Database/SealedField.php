<?php namespace Tempest\Database;

/**
 * A real-only field declaration.
 *
 * @author Marty Wallace
 */
class SealedField {

	/** @var string */
	protected $type;

	/** @var bool */
	protected $increments = false;

	/** @var mixed */
	protected $default = null;

	/** @var bool */
	protected $nullable = true;

	protected function __construct(Field $field) {
		$this->type = $field->getType();
		$this->increments = $field->isAutoIncrement();
		$this->default = $field->getDefault();
		$this->nullable = $field->isNullable();
	}

	/**
	 * Retrieve the field type.
	 *
	 * @return string
	 */
	public function getType() {
		return $this->type;
	}

	/**
	 * Retrieve the default value for this field.
	 *
	 * @return mixed
	 */
	public function getDefault() {
		return $this->default;
	}

	/**
	 * Whether or not this field auto-increments.
	 *
	 * @return bool
	 */
	public function isAutoIncrement() {
		return $this->increments;
	}

	/**
	 * Whether or not this field is nullable.
	 *
	 * @return bool
	 */
	public function isNullable() {
		return $this->nullable;
	}

}