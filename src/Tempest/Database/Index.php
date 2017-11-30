<?php namespace Tempest\Database;

use JsonSerializable;

/**
 * An index on a field.
 *
 * @author Marty Wallace
 */
class Index implements JsonSerializable {

	const PRIMARY = 'primary';
	const UNIQUE = 'unique';
	const INDEX = 'index';

	/** @var SealedField[] */
	private $_fields;

	/** @var string */
	private $_type;

	/** @var string */
	private $_name;

	/**
	 * @param string $type The index type.
	 * @param SealedField[] $fields The field this index was declared by.
	 * @param string $name The index name, useful for constructing composite indexes.
	 */
	public function __construct($type = self::INDEX, array $fields = [], $name = null) {
		$this->_type = $type;
		$this->_fields = $fields;
		$this->_name = $name;
	}

	/**
	 * @return SealedField[]
	 */
	public function getFields() {
		return $this->_fields;
	}

	/**
	 * @return string
	 */
	public function getType() {
		return $this->_type;
	}

	/**
	 * @return string
	 */
	public function getName() {
		return $this->_name;
	}

	/**
	 * Create a copy of this index.
	 *
	 * @return Index
	 */
	public function copy() {
		return new static($this->getType(), $this->getFields(), $this->getName());
	}

	/**
	 * Merge this index with another index of the same name.
	 *
	 * @param Index $index The index to merge with.
	 */
	public function merge(Index $index) {
		$this->_fields = array_merge($this->_fields, $index->getFields());
	}

	public function jsonSerialize() {
		return [
			'type' => $this->_type,
			'name' => $this->_name,
			'fields' => $this->_fields
		];
	}

}