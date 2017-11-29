<?php namespace Tempest\Database;

/**
 * An index on a field.
 *
 * @author Marty Wallace
 */
class Index {

	const PRIMARY = 'primary';
	const UNIQUE = 'unique';
	const INDEX = 'index';

	/** @var string */
	private $_type;

	/** @var string */
	private $_name;

	/**
	 * Index constructor.
	 *
	 * @param string $type The index type.
	 * @param string $name The index name, useful for constructing composite indexes.
	 */
	public function __construct($type = self::INDEX, $name = null) {
		$this->_type = $type;
		$this->_name = $name;
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

}