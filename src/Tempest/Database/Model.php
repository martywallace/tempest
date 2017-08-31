<?php namespace Tempest\Database;

use Exception;
use ReflectionClass;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Doctrine\Common\Inflector\Inflector;

/**
 * A database model, derived from a {@link Row}.
 *
 * @author Marty Wallace
 */
abstract class Model extends EventDispatcher {

	/** @var SealedField[] */
	private static $_fields = null;

	/**
	 * Retrieve all declared fields for this model.
	 *
	 * @return SealedField[]
	 */
	public static function getFields() {
		if (empty(static::$_fields)) {
			return array_map(function(Field $field) {
				return $field->seal();
			}, static::fields());
		}

		return static::$_fields;
	}

	/**
	 * Determine whether this model has declared a field.
	 *
	 * @param string $field The field name.
	 *
	 * @return bool
	 */
	public static function hasField($field) {
		return array_key_exists($field, static::getFields());
	}

	/**
	 * Retrieve a single field declared by this model.
	 *
	 * @param string $field The field name.
	 *
	 * @return SealedField
	 */
	public static function getField($field) {
		return static::hasField($field) ? static::getFields()[$field] : null;
	}

	/**
	 * Create one or more models from one or more rows.
	 *
	 * @param Row|Row[] $rows One or more rows. If a single row is provided, a single model is returned. If an array of
	 * rows is provided, an array of models is returned, even if that array only contains a single item. If the provided
	 * array is empty, an empty array is returned. If the provided value is not an array and empty, null is returned.
	 *
	 * @return static|static[]
	 */
	public static function from($rows) {
		if (is_array($rows)) {
			return array_values(array_map(function(Row $row) {
				return new static($row->getValues());
			}, $rows));
		}

		return empty($rows) ? null : new static($rows->getValues());
	}

	/**
	 * Provide reflection information about this model.
	 *
	 * @return ReflectionClass
	 */
	public static function reflect() {
		return new ReflectionClass(static::class);
	}

	/**
	 * Create a new model and optionally pre-populate it with data.
	 *
	 * @param array $data Optional data to populate the newly created model with.
	 *
	 * @return static
	 */
	public static function create(array $data = []) {
		return new static($data);
	}

	/**
	 * Perform a database migration for the table associated with this model.
	 *
	 * @return array The performed queries.
	 */
	public static function migrate() {
		$queries = [];

		return $queries;
	}

	/*
	 * The known fields for this model.
	 *
	 * @return Field[]
	 */
	protected abstract static function fields();

	/**
	 * The table that stores this model type.
	 *
	 * @return string
	 */
	protected static function table() {
		return Inflector::pluralize(Inflector::tableize(static::reflect()->getShortName()));
	}

	/** @var mixed[] */
	private $_data = [];

	/** @var mixed[] */
	private $_undeclared = [];

	/**
	 * Model constructor.
	 *
	 * @param array $data
	 */
	private function __construct(array $data = []) {
		$this->reset()->fill($data);
	}

	public function __get($prop) {
		if (static::hasField($prop)) return $this->getFieldValue($prop);
		if (array_key_exists($prop, $this->_undeclared)) return $this->_undeclared[$prop];

		return null;
	}

	public function __set($prop, $value) {
		if (static::hasField($prop)) $this->setFieldValue($prop, $value);
		else $this->_undeclared[$prop] = $value;
	}

	/**
	 * Fill this model with data.
	 *
	 * @param array $data The data to fill the model with.
	 *
	 * @return $this
	 */
	public function fill(array $data) {
		foreach ($data as $field => $value) {
			if (static::hasField($field)) $this->setFieldValue($field, $value);
			else $this->_undeclared[$field] = $value;
		}

		return $this;
	}

	/**
	 * Reset this model's data - all undeclared data will be deleted and all field values will be set to their defaults.
	 *
	 * @return $this
	 */
	public function reset() {
		$this->_undeclared = [];

		/** @var Field $field */
		foreach (static::getFields() as $name => $field) {
			$this->_data[$name] = $field->getDefault();
		}

		return $this;
	}

	/**
	 * Get the value attached to a specified field.
	 *
	 * @param string $field The field name.
	 *
	 * @return mixed
	 *
	 * @throws Exception I this model does not declare the provided field.
	 */
	public function getFieldValue($field) {
		if (!static::hasField($field)) {
			throw new Exception('Model "' . static::reflect()->getShortName() . '" does not declare a field "' . $field . '".');
		}

		return $this->_data[$field];
	}

	/**
	 * Sets the value of a field declared by this model.
	 *
	 * @param string $field The field to set.
	 * @param mixed $value The value to assign.
	 *
	 * @return $this
	 *
	 * @throws Exception If this model does not declare the provided field.
	 */
	public function setFieldValue($field, $value) {
		if (!static::hasField($field)) {
			throw new Exception('Model "' . static::reflect()->getShortName() . '" does not declare a field "' . $field . '".');
		}

		$this->_data[$field] = $value;

		return $this;
	}

}