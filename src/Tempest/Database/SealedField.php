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
	const ATTR_RPRIMARY_KEY = 'primaryKey';

	const KEY_UNIQUE = 'unique';
	const KEY_INDEX = 'index';

	/** @var string */
	private $_name;

	/** @var mixed[] */
	private $_attributes = [
		self::ATTR_TYPE => null,
		self::ATTR_AUTO_INCREMENT => false,
		self::ATTR_DEFAULT => null,
		self::ATTR_NULLABLE => true,
		self::ATTR_RPRIMARY_KEY => false
	];

	/** @var array */
	private $_keys = [
		self::KEY_UNIQUE => [],
		self::KEY_INDEX => []
	];

	protected function __construct($name, Field $field) {
		$this->_name = $name;

		$this->setAttrs($field->getAttrs());
		$this->setKeys($field->getKeys());
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
	 * Adds a key for this field.
	 *
	 * @param string $type The key type.
	 * @param string|bool $value The key name in the case of a compound key, or true for non-compound.
	 */
	protected function addKey($type, $value) {
		$this->_keys[$type][] = $value;
	}

	/**
	 * Get a key.
	 *
	 * @param string $type The key type.
	 *
	 * @return string[]
	 */
	protected function getKey($type) {
		return $this->_keys[$type];
	}

	/**
	 * Set multiple keys.
	 *
	 * @param array $keys The keys to set.
	 */
	protected function setKeys(array $keys) {
		$this->_keys = $keys;
	}

	/**
	 * Get all keys.
	 *
	 * @return string[][]
	 */
	protected function getKeys() {
		return $this->_keys;
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
	public function isAutoIncrementing() {
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

	/**
	 * Whether or not this field is part of a primary key.
	 *
	 * @return bool
	 */
	public function hasPrimaryKey() {
		return $this->getAttr(self::ATTR_RPRIMARY_KEY);
	}

	/**
	 * Whether or not this field is part of a unique key.
	 *
	 * @return bool
	 */
	public function hasUniqueKey() {
		return count($this->getKey(self::KEY_UNIQUE)) > 0;
	}

	/**
	 * Whether or not this field has an index.
	 *
	 * @return bool
	 */
	public function hasIndex() {
		return count($this->getKey(self::KEY_INDEX)) > 0;
	}

	/**
	 * Whether or not this field is unique, based on whether it has either a {@link hasPrimaryKey PRIMARY} or
	 * {@link hasUniqueKey UNIQUE} key.
	 *
	 * @return bool
	 */
	public function isUnique() {
		return $this->hasPrimaryKey() || $this->hasUniqueKey();
	}

}