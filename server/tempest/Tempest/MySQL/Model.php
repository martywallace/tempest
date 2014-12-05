<?php namespace Tempest\MySQL;

use PDOStatement;


/**
 * Describes a row in the database.
 * @author Marty Wallace.
 */
class Model
{

	/**
	 * @var string The name of the table that stores instances of this model.
	 */
	protected $table = null;

	/**
	 * @var string The name of the table's primary key.
	 */
	protected $primary = null;

	/**
	 * @var bool Whether the primary key is an AUTO_INCREMENT column. If yes, the primary key value will automatically update when a new instance of this model is inserted into the database.
	 */
	protected $autoIncrement = true;

	/**
	 * @var array The list of columns in the table associated with this model.
	 */
	protected $fields = array();

	/**
	 * @var array The list of read-only columns that will not be updated in a save() call.
	 */
	protected $readonly = array();


	/**
	 * Search for an instance of a Model stores in the database using its primary key value.
	 * @param Database $db A reference to a Database object.
	 * @param mixed $primary The primary key value to search for.
	 * @param array $fields An optional array of fields that should be returned by the query.
	 * @return Model The resulting Model instance.
	 */
	public static function find(Database $db, $primary, Array $fields = null)
	{
		$i = new static();

		$stmt = $db->prepare("SELECT " . ($fields === null ? '*' : implode(',', $fields)) . " FROM {$i->getTable()} WHERE {$i->getPrimary()} = :primary");
		$db->execute($stmt, array(":primary" => $primary));

		return $stmt->fetchObject(get_class($i));
	}


	/**
	 * Deletes a record from the database.
	 * @param Database $db A reference to the Database object.
	 * @param mixed $primary The primary key value of the record to delete.
	 * @return PDOStatement The PDOStatement used to delete the record.
	 */
	public static function delete(Database $db, $primary)
	{
		$i = new static();

		$stmt = $db->prepare("DELETE FROM {$i->getTable()} WHERE {$i->getPrimary()} = :primary");
		$db->execute($stmt, array(":primary" => $primary));

		return $stmt;
	}


	/**
	 * Constructor.
	 * @param Array $data An optional array of starting data, for convenience.
	 */
	public function __construct(Array $data = null)
	{
		if($data !== null)
		{
			$this->multiset($data, true);
		}
	}


	/**
	 * Determine whether this Model defines the field / column with a specified name.
	 * @param string $name The field / column name.
	 * @return bool
	 */
	public function defines($name)
	{
		return in_array($name, $this->fields);
	}


	/**
	 * Get the value of a field / column within this Model.
	 * @param string $name The field name.
	 * @param mixed $default A default value, if the field / column does not exist or has no value.
	 * @return mixed
	 */
	public function get($name, $default = null)
	{
		if(property_exists($this, $name)) return $this->{$name};
		return $default;
	}


	/**
	 * Updates the value of a field / column.
	 * @param string $name The name of the field / column.
	 * @param mixed $value The new value.
	 */
	public function set($name, $value)
	{
		if($this->defines($name))
		{
			$this->{$name} = $value;
		}
		else
		{
			// This model does not define the target property.
			trigger_error("Property <code>{$name}</code> is not defined by <code>" . get_class($this) . '</code>.');
		}
	}


	/**
	 * Updates the value of multiple fields / columns.
	 * @param array $data An array of potentially related data to set on this Model.
	 * @param bool $readonly Whether or not readonly values should be included in the update.
	 */
	public function multiset(Array $data, $readonly = false)
	{
		foreach($data as $field => $value)
		{
			if($this->defines($field))
			{
				if(in_array($field, $this->readonly) && !$readonly)
				{
					// This field is included in the readonly list, but we haven't said that we want to
					// update readonly values; skip it.
					continue;
				}

				$this->set($field, $value);
			}
		}
	}


	/**
	 * Obtain a JSON representation of this Model.
	 * @return string A JSON string representing this Model.
	 */
	public function toJSON()
	{
		$data = array();

		foreach($this->fields as $field)
		{
			$data[$field] = $this->get($field);
		}

		return json_encode($data, JSON_NUMERIC_CHECK);
	}


	/**
	 * Validate this model and return the errors in an array.
	 * @param Database $db Reference to a Database object.
	 * @return array An array containing any validation errors.
	 */
	public function validate(Database $db)
	{
		return array();
	}


	/**
	 * @param Database $db Reference to a Database object.
	 * @return PDOStatement The PDOStatement used to save this model to MySQL.
	 */
	public function save(Database $db)
	{
		$data = array();
		$updates = array();
		$inserts = array();

		foreach($this->fields as $field)
		{
			$data[":$field"] = $this->get($field);
			$inserts[] = ":$field";

			if(!in_array($field, $this->readonly) && $field !== $this->primary)
			{
				// This is a field that can be updated.
				$updates[] = "$field = :$field";
			}
		}

		$query = "INSERT INTO {$this->table} (" . implode(',', $this->fields) . ") VALUES(" . implode(',', $inserts) . ")
			ON DUPLICATE KEY UPDATE " . implode(',', $updates);


		$stmt = $db->prepare($query);

		// Execute the PDOStatement via Database to enable Tempest errors.
		$db->execute($stmt, $data);

		if($this->get($this->primary) === null && $this->autoIncrement)
		{
			// Update the primary key if this is a new instance and the column is AUTO_INCREMENT.
			$this->set($this->primary, $db->lastInsertId());
		}

		return $stmt;
	}


	/**
	 * @return string The name of the table associated with this model.
	 */
	public function getTable(){ return $this->table; }


	/**
	 * @return string The name of the primary key associated with this model.
	 */
	public function getPrimary(){ return $this->primary; }

}