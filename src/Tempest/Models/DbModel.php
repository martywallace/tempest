<?php namespace Tempest\Models;

use DateTime;
use Tempest\Tempest;
use Tempest\Utils\JSONUtil;


/**
 * A model stored in the database.
 *
 * @package Tempest\Models
 * @author Marty Wallace
 */
abstract class DbModel extends Model {

	const FIELD_INT = 'int';
	const FIELD_STRING = 'string';
	const FIELD_JSON = 'json';
	const FIELD_DATETIME = 'dateTime';

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
		} else {
			// Fields fetched from PDO.
			$fields = array_keys($this->defineFields());

			foreach ($fields as $field) {
				if (!empty($this->{$field})) {
					$mutated = $this->mutate($field, $this->{$field}, function($type, $value) {
						switch ($type) {
							default: return $value; break;

							case self::FIELD_INT: return intval($value); break;
							case self::FIELD_JSON: return JSONUtil::decode($value); break;
							case self::FIELD_DATETIME: return new DateTime($value); break;
						}
					});

					$this->__set($field, $mutated);
				}
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

	/**
	 * Saves this model to its table in the database.
	 *
	 * @param callable|callable[] $validator A method or array of methods to use to validate this model before
	 * attempting to save it. The validators should return an array of error messages mapped to keys with the same name
	 * as the fields being validated, e.g.
	 *
	 * <pre>
	 * return array(
	 *     'email' => 'Email address is invalid',
	 *     'postcode' => 'Postcode is too long'
	 * );
	 * </pre>
	 *
	 * @return array The array produced by the validators, or an empty array.
	 */
	public function save($validator = null) {
		$result = array();

		if (!empty($validator)) {
			if (is_callable($validator)) {
				$validator = array($validator);
			}

			foreach ($validator as $method) {
				$result = array_replace_recursive($result, $method($this));
			}
		}

		if (empty($result)) {
			$fields = array_keys($this->_data);
			$placeholders = array_map(function($value) { return ':' . $value; }, $fields);
			$update = array_map(function($value) { return $value . '= :' . $value; }, $fields);

			$query = 'INSERT INTO ' . $this->getTable() . ' (' . implode(',', $fields) . ') VALUES(' . implode(',', $placeholders) . ')
				ON DUPLICATE KEY UPDATE ' . implode(',', $update);

			Tempest::get()->db->query($query, $this->getPrimitiveData());

			if (empty($this->{$this->definePrimary()})) {
				$this->{$this->definePrimary()} = Tempest::get()->db->lastInsertId;
			}
		}

		return $result;
	}

	/**
	 * Returns data mutated based on the data type provided in the {@link defineFields()} definition.
	 *
	 * @param callable $mutator A method used to mutate the data. It accepts the field type and the value to mutate and
	 * should return the mutated value.
	 *
	 * @return mixed[]
	 */
	public function getMutatedData(callable $mutator) {
		$mutated = array();

		foreach ($this->getData() as $field => $value) {
			$mutated[$field] = $this->mutate($field, $value, $mutator);
		}

		return $mutated;
	}

	/**
	 * Mutate a field based on its type.
	 *
	 * @param string $field The field to mutate.
	 * @param mixed $value The value of the field.
	 * @param callable $mutator A method used to mutate the value. It accepts the field type and the value to mutate and
	 * should return the mutated value.
	 *
	 * @return mixed
	 */
	public function mutate($field, $value, callable $mutator) {
		if ($value !== null) {
			return $mutator($this->getFieldType($field), $value);
		}

		return null;
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

	/**
	 * Returns the user defined type for a field.
	 *
	 * @param string $field The field to get the type for.
	 *
	 * @return string
	 */
	public function getFieldType($field) {
		return $this->hasField($field) ? $this->getFields()[$field] : null;
	}

	/**
	 * Returns the current data for this model.
	 *
	 * @return mixed[]
	 */
	public function getData() {
		return $this->_data;
	}

	/**
	 * Returns a primitive representation of the data stored in this model, ready for saving in the database.
	 */
	public function getPrimitiveData() {
		return $this->getMutatedData(function($type, $value) {
			switch ($type) {
				default: return $value; break;

				case self::FIELD_JSON: return JSONUtil::encode($value); break;
				case self::FIELD_DATETIME: return $value->format('Y-m-d H:i:s'); break;
			}
		});
	}

	public function jsonSerialize() {
		return $this->_data;
	}

}