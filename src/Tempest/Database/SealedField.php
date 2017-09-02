<?php namespace Tempest\Database;

/**
 * A real-only field declaration.
 *
 * @author Marty Wallace
 */
class SealedField {

	const ATTR_TYPE = 'type';
	const ATTR_AUTO_INCREMENT = 'autoIncrement';
	const ATTR_NULLABLE = 'nullable';
	const ATTR_DEFAULT = 'default';
	const ATTR_PRIMARY = 'primary';
	const ATTR_UNIQUE = 'unique';
	const ATTR_INDEX = 'index';

	/** @var mixed[] */
	private $_attributes = [
		self::ATTR_TYPE => null,
		self::ATTR_AUTO_INCREMENT => false,
		self::ATTR_DEFAULT => null,
		self::ATTR_NULLABLE => true,
		self::ATTR_PRIMARY => false,
		self::ATTR_UNIQUE => false,
		self::ATTR_INDEX => false
	];

	protected function __construct(Field $field) {
		$this->setAttrs($field->getAttrs());
	}

	/**
	 * Sets a field attribute.
	 *
	 * @param string $attribute The attribute to set.
	 * @param mixed $value The value to set the attribute to.
	 */
	protected function setAttr($attribute, $value) {
		if (in_array($attribute, $this->_attributes)) {
			$this->_attributes[$attribute] = $value;
		}
	}

	/**
	 * Set multiple attributes.
	 *
	 * @param array $attributes The attributes and their values.
	 */
	protected function setAttrs(array $attributes) {
		foreach ($attributes as $attribute => $value) {
			$this->setAttr($attribute, $value);
		}
	}

	/**
	 * Retrieve an attribute value.
	 *
	 * @param string $attribute The attribute to get.
	 *
	 * @return mixed
	 */
	protected function getAttr($attribute) {
		if (in_array($attribute, $this->_attributes)) {
			return $this->_attributes[$attribute];
		}

		return null;
	}

	/**
	 * Retrieve all attributes.
	 *
	 * @return mixed[]
	 */
	protected function getAttrs() {
		return $this->_attributes;
	}

	/**
	 * Retrieve the field type.
	 *
	 * @return string
	 */
	public function getType() {
		return $this->getAttr(self::ATTR_TYPE);
	}

	/**
	 * Retrieve the default value for this field.
	 *
	 * @return mixed
	 */
	public function getDefault() {
		return $this->getAttr(self::ATTR_DEFAULT);
	}

	/**
	 * Whether or not this field auto-increments.
	 *
	 * @return bool
	 */
	public function isAutoIncrement() {
		return $this->getAttr(self::ATTR_AUTO_INCREMENT);
	}

	/**
	 * Whether or not this field is nullable.
	 *
	 * @return bool
	 */
	public function isNullable() {
		return $this->getAttr(self::ATTR_NULLABLE);
	}

}