<?php namespace Tempest\Models;

use Tempest\Tempest;


/**
 * A model stored in the database.
 *
 * @package Tempest\Models
 * @author Marty Wallace
 */
abstract class DbModel extends Model {

	protected static $_instance = null;

	/**
	 * The name of the table used to store this model type.
	 *
	 * @return string
	 */
	public static function getTable() {
		return static::_get()->defineTable();
	}

	/**
	 * The field used as the primary key for this table.
	 *
	 * @return string
	 */
	public static function getPrimary() {
		return static::_get()->definePrimary();
	}

	/**
	 * All the fields that belong to this table.
	 *
	 * @return mixed[]
	 */
	public static function getFields() {
		return static::_get()->defineFields();
	}

	/**
	 * Returns one instance of this class. Used to extract instance-level information like table name.
	 *
	 * @return DbModel
	 */
	private static function _get() {
		if (empty(static::$_instance)) {
			static::$_instance = new static();
		}

		return static::$_instance;
	}

	/** @var mixed[] */
	private $_data = array();

	/** @var mixed[] */
	private $_extra = array();

	protected abstract function defineFields();
	protected abstract function defineTable();
	protected abstract function definePrimary();

	public function __construct(array $initial = array()) {
		if (!empty($initial)) {
			foreach ($initial as $prop => $value) {
				$this->__set($prop, $value);
			}
		}
	}

	public function __get($prop) {
		return $this->hasField($prop)
			? (array_key_exists($prop, $this->_data) ? $this->_data[$prop] : null)
			: (array_key_exists($prop, $this->_extra) ? $this->_extra[$prop] : null);
	}

	public function __set($prop, $value) {
		if ($this->hasField($prop)) {
			$this->_data[$prop] = $value;
		} else {
			$this->_extra[$prop] = $value;
		}
	}

	public function save() {
		$result = array();

		$fields = array_keys($this->_data);
		$placeholders = array_map(function($value) { return ':' . $value; }, $fields);
		$update = array_map(function($value) { return $value . '= :' . $value; }, $fields);

		$query = 'INSERT INTO ' . $this->getTable() . ' (' . implode(',', $fields) . ') VALUES(' . implode(',', $placeholders) . ')
			ON DUPLICATE KEY UPDATE ' . implode(',', $update);

		Tempest::get()->db->query($query, $this->_data);

		if (empty($this->{$this->definePrimary()})) {
			$this->{$this->definePrimary()} = Tempest::get()->db->lastInsertId;
		}

		return $result;
	}

	/**
	 * Determine whether or not this model has the specified field.
	 *
	 * @param string $field The field to check for.
	 *
	 * @return bool
	 */
	public function hasField($field) {
		return array_key_exists($field, $this->defineFields());
	}

	public function jsonSerialize() {
		return $this->_data;
	}

}