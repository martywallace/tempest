<?php namespace Tempest\Database;

use Exception;
use ReflectionClass;
use JsonSerializable;
use Tempest\App;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Doctrine\Common\Inflector\Inflector;
use Tempest\Events\ModelEvent;

/**
 * A database model, derived from a {@link Row}.
 *
 * @author Marty Wallace
 */
abstract class Model extends EventDispatcher implements JsonSerializable {

	/** @var SealedField[] */
	private static $_fields = [];

	/** @var Index[] */
	private static $_indexes = [];

	/**
	 * Get the table name associated with this model.
	 *
	 * @return string
	 */
	public static function getTable() {
		return static::table();
	}

	/**
	 * Gets a generated CREATE TABLE query for this model.
	 *
	 * @return string
	 */
	public static function getCreateTableQuery() {
		$content = [];

		foreach (static::getFields() as $field) {
			$content[] = trim(implode(' ', [
				'`' . $field->getName() . '`',
				$field->getColumnType(),
				$field->getNullable() ? 'DEFAULT NULL' : 'NOT NULL',
				$field->getAutoIncrements() ? 'AUTO_INCREMENT' : ''
			]));
		}

		foreach (static::getIndexes() as $index) {
			$fieldNames = array_map(function(SealedField $field) {
				return '`' . $field->getName() . '`';
			}, $index->getFields());

			if ($index->getType() === Index::PRIMARY) $key = 'PRIMARY KEY';
			else if ($index->getType() === Index::UNIQUE) $key = 'UNIQUE KEY';
			else $key = 'KEY';

			$content[] = trim(implode(' ', [
				$key . ($index->getName() ? '`' . $index->getName() . '`' : ''),
				'(' . implode(', ', $fieldNames) . ')'
			]));
		}

		return 'CREATE TABLE `' . static::getTable() . '` (' . PHP_EOL . '  ' . implode(',' . PHP_EOL . '  ', $content) . PHP_EOL . ');';
	}

	/**
	 * Retrieve all declared fields for this model.
	 *
	 * @return SealedField[]
	 */
	public static function getFields() {
		if (!array_key_exists(static::class, self::$_fields)) {
			$sealed = [];

			foreach (static::fields() as $name => $field) {
				$sealed[$name] = $field->seal($name);
			}

			self::$_fields[static::class] = $sealed;
		}

		return self::$_fields[static::class];
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
	 * Get all indexes attached to this model, as declared by its {@link fields}. This does not return a flat tree of
	 * indexes as defined by each field, but will merge indexes with matching names and a list of all the associated
	 * fields.
	 *
	 * @return Index[]
	 *
	 * @throws Exception If the index tree could not be properly merged.
	 */
	public static function getIndexes() {
		if (!array_key_exists(static::class, self::$_indexes)) {
			/** @var Index[] $indexes */
			$indexes = [];

			foreach (static::getFields() as $field) {
				foreach ($field->getIndexes() as $index) {
					if (!empty($index->getName()) || $index->getType() === Index::PRIMARY) {
						foreach ($indexes as $known) {
							// Merge keys that are both PRIMARY or whose names match.
							$shouldMerge = $known->getName() === $index->getName()
								|| ($known->getType() === Index::PRIMARY && $index->getType() === Index::PRIMARY);

							if ($shouldMerge) {
								if ($known->getType() !== $index->getType()) {
									throw new Exception('Indexes for "' . static::class . '" sharing a name must also share their type.');
								}

								$known->merge($index);

								continue 2;
							}
						}

						$indexes[] = $index->copy();
					} else {
						$indexes[] = $index->copy();
					}
				}
			}

			self::$_indexes[static::class] = $indexes;
		}

		return self::$_indexes[static::class];
	}

	/**
	 * Retrieve an index using its name.
	 *
	 * @param string $name The index name as declared by this model.
	 *
	 * @return Index
	 */
	public static function getIndexByName($name) {
		foreach (static::getIndexes() as $index) {
			if ($index->getName() === $name) {
				return $index;
			}
		}

		return null;
	}

	/**
	 * Gets the PRIMARY index if this model declares one.
	 *
	 * @return Index
	 */
	public static function getPrimaryIndex() {
		foreach (static::getIndexes() as $index) {
			if ($index->getType() === Index::PRIMARY) {
				return $index;
			}
		}

		return null;
	}

	/**
	 * Gets all PRIMARY or UNIQUE indexes declared by this model.
	 *
	 * @return Index[]
	 */
	public static function getUniqueIndexes() {
		return array_filter(static::getIndexes(), function(Index $index) {
			return $index->getType() === Index::PRIMARY
				|| $index->getType() === INDEX::UNIQUE;
		});
	}

	/**
	 * Get all INDEX (non PRIMARY or UNIQUE) indexes declared by this model.
	 *
	 * @return Index[]
	 */
	public static function getNonUniqueIndexes() {
		return array_filter(static::getIndexes(), function(Index $index) {
			return $index->getType() === Index::INDEX;
		});
	}

	/**
	 * Get all non PRIMARY indexes declared by this model.
	 *
	 * @return Index[]
	 */
	public static function getNonPrimaryIndexes() {
		return array_filter(static::getIndexes(), function(Index $index) {
			return $index->getType() !== Index::PRIMARY;
		});
	}

	/**
	 * Retrieve all fields that are PRIMARY keyed.
	 *
	 * @return SealedField[]
	 */
	public static function getPrimaryFields() {
		return static::getPrimaryIndex()->getFields();
	}

	/**
	 * Retrieve all fields that are either PRIMARY or UNIQUE keyed.
	 *
	 * @return SealedField[]
	 */
	public static function getUniqueFields() {
		return array_filter(static::getFields(), function(SealedField $field) {
			return $field->hasUniqueIndex();
		});
	}

	/**
	 * Retrieve all fields that are not PRIMARY or UNIQUE keyed.
	 *
	 * @return SealedField[]
	 */
	public static function getNonUniqueFields() {
		return array_filter(static::getFields(), function(SealedField $field) {
			return !$field->hasUniqueIndex();
		});
	}

	/**
	 * Retrieve all fields that are not PRIMARY keyed.
	 *
	 * @return SealedField[]
	 */
	public static function getNonPrimaryFields() {
		return array_filter(static::getFields(), function(SealedField $field) {
			return !$field->hasPrimaryIndex();
		});
	}

	/**
	 * Retrieve all fields with no index associated with it.
	 *
	 * @return SealedField[]
	 */
	public static function getNonIndexedFields() {
		return array_filter(static::getFields(), function(SealedField $field) {
			return count($field->getIndexes()) === 0;
		});
	}

	/**
	 * Retrieve the auto-incrementing field,  if this model declared one.
	 *
	 * @return SealedField
	 */
	public static function getIncrementingField() {
		foreach (static::getFields() as $field) {
			if ($field->getAutoIncrements()) return $field;
		}

		return null;
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
	 * Returns a SELECT query that resolves to one or more instances of this model.
	 *
	 * @param string[] $fields The fields to select.
	 *
	 * @return Query
	 */
	public static function select(array $fields = ['*']) {
		return Query::select(static::getTable(), $fields)->produces(static::class);
	}

	/**
	 * Returns a DELETE query for the table associated with this model.
	 *
	 * @return Query
	 */
	public static function delete() {
		return Query::delete(static::getTable());
	}

	/**
	 * Returns an INSERT INTO query for the table associated with this model.
	 *
	 * @param array $data The data to insert.
	 *
	 * @return Query
	 */
	public static function insert(array $data = []) {
		return Query::insert(static::getTable(), $data);
	}

	/**
	 * Find an instance of this model using its primary key, assuming it declares a single primary key.
	 *
	 * @param mixed $primary The primary key value.
	 *
	 * @return static
	 *
	 * @throws Exception If there is not exactly one primary key declared by this model.
	 */
	public static function find($primary) {
		$fields = static::getPrimaryFields();

		if (count($fields) === 0) throw new Exception('There are no primary keys declared by "' . static::reflect()->getShortName() . '".');
		if (count($fields) > 1) throw new Exception('"' . static::reflect()->getShortName() . '" defines multiple primary keys.');

		return static::select()->where(array_pop($fields)->getName(), $primary)->first();
	}

	/**
	 * Attempt to find a record with the specfied primary key, else creates a new one. The record is not automatically
	 * saved to the database.
	 *
	 * @param mixed $primary The primary key value.
	 * @param array $data Data to fill the existing or newly created model with.
	 *
	 * @return static
	 */
	public static function findOrCreate($primary, array $data = []) {
		$model = static::find($primary);

		if (empty($model)) {
			$model = static::create();
		}

		$model->fill($data);

		return $model;
	}

	/**
	 * Retrieve all rows within the table associated with this model and map them to this model.
	 *
	 * @return static[]
	 */
	public static function all() {
		return static::from(Query::select(static::getTable())->all());
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
	 * Create a new model and optionally {@link fill} it with data.
	 *
	 * @param array $data Optional data to {@link fill} the newly created model with.
	 *
	 * @return static
	 */
	public static function create(array $data = []) {
		return new static($data);
	}

	/**
	 * Declares fields for this model.
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
		if (static::hasField($prop)) return $this->getRefined($prop);
		if (array_key_exists($prop, $this->_undeclared)) return $this->_undeclared[$prop];

		return null;
	}

	public function __set($prop, $value) {
		if (static::hasField($prop)) $this->setFieldValue($prop, $value);
		else $this->_undeclared[$prop] = $value;
	}

	public function __isset($prop) {
		return static::hasField($prop) || array_key_exists($prop, $this->_undeclared);
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
			$this->setFieldValue($name, $field->getDefault());
		}

		$this->dispatch(ModelEvent::RESET, new ModelEvent($this));

		return $this;
	}

	/**
	 * Get the raw value attached to a specified field.
	 *
	 * @param string $field The field name.
	 *
	 * @return mixed
	 *
	 * @throws Exception I this model does not declare the provided field.
	 */
	public function getRaw($field) {
		if (!static::hasField($field)) {
			throw new Exception('Model "' . static::reflect()->getShortName() . '" does not declare a field "' . $field . '".');
		}

		return static::getField($field)->toRaw($this->_data[$field]);
	}

	/**
	 * Get the refined value attached to a specified field.
	 *
	 * @param string $field The field name.
	 *
	 * @return mixed
	 *
	 * @throws Exception I this model does not declare the provided field.
	 */
	public function getRefined($field) {
		if (!static::hasField($field)) {
			throw new Exception('Model "' . static::reflect()->getShortName() . '" does not declare a field "' . $field . '".');
		}

		return static::getField($field)->toRefined($this->_data[$field]);
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

		$this->_data[$field] = static::getField($field)->toRaw($value);

		return $this;
	}

	/**
	 * Get all raw values.
	 *
	 * @return array
	 */
	public function getAllRaw() {
		$result = [];

		foreach ($this->_data as $field => $value) {
			$result[$field] = static::getField($field)->toRaw($value);
		}

		return $result;
	}

	/**
	 * Get all refined values.
	 *
	 * @return array
	 */
	public function getAllRefined() {
		$result = [];

		foreach ($this->_data as $field => $value) {
			$result[$field] = static::getField($field)->toRefined($value);
		}

		return $result;
	}

	/**
	 * Get all raw values from non-unique fields.
	 *
	 * @return array
	 */
	public function getNonUniqueRaw() {
		$result = [];

		foreach (static::getNonUniqueFields() as $field) {
			$result[$field->getName()] = $this->getRaw($field->getName());
		}

		return $result;
	}

	/**
	 * Get all raw values from non-primary fields.
	 *
	 * @return array
	 */
	public function getNonPrimaryRaw() {
		$result = [];

		foreach (static::getNonPrimaryFields() as $field) {
			$result[$field->getName()] = $this->getRaw($field->getName());
		}

		return $result;
	}

	/**
	 * Gets the primary key value in the case where this model has a single primary key.
	 *
	 * @return mixed
	 *
	 * @throws Exception If there is not exactly one primary key.
	 */
	public function getPrimaryKey() {
		$primary = static::getPrimaryFields();

		if (count($primary) === 0) throw new Exception('There are no primary keys declared by "' . static::reflect()->getShortName() . '".');
		if (count($primary) > 1) throw new Exception('"' . static::reflect()->getShortName() . '" defines multiple primary keys.');

		return $this->getRefined(array_pop($primary)->getName());
	}

	/**
	 * Saves this model into the database.
	 *
	 * @param bool $updateOnDuplicate Whether or not to update a matching duplicate record if one was found.
	 */
	public function save($updateOnDuplicate = true) {
		$this->dispatch(ModelEvent::BEFORE_SAVE, new ModelEvent($this));

		$query = static::insert($this->_data);

		if ($updateOnDuplicate) {
			$query = $query->onDuplicateKeyUpdate($this->getNonPrimaryRaw());
		}

		$query->execute();

		$incrementing = static::getIncrementingField();

		if (!empty($incrementing)) {
			// This model has a field that should auto-increment.
			if (empty($this->getRaw($incrementing->getName()))) {
				// There is no existing value for this field, set it to the last insert ID.
				$this->setFieldValue($incrementing->getName(), App::get()->db->getLastInsertId());
			}
		}

		$this->dispatch(ModelEvent::AFTER_SAVE, new ModelEvent($this));
	}

	public function jsonSerialize() {
		return $this->getAllRefined();
	}

}