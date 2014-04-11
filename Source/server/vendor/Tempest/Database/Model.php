<?php namespace Tempest\Database;

use ReflectionClass;
use ReflectionProperty;
use Tempest\Database\Database;


class Model
{

	protected $table = null;
	protected $primary = 'id';
	protected $readonly = array('id');


	public static function find($primary, Array $columns = null)
	{
		$def = new static();
		$stmt = Database::prepare("SELECT " . ($columns === null ? "*" : str_comma_join($columns)) . " FROM {$def->table} WHERE {$def->primary} = :primary");
		$stmt->execute(array(":primary" => $primary));

		$result = $stmt->fetchObject($def->name());

		if($result === false)
		{
			Database::error($stmt, "Could not find an instance of '" . $def->name() . "' where '{$def->primary}' is '$primary'");
			return null;
		}

		return $result;
	}


	public static function create(Array $primitive)
	{
		$def = new static();

		$columns = array_keys($primitive);
		$values = array_values($primitive);
		$markers = array_map(function($x){ return ":$x"; }, $columns);

		$stmt = Database::prepare("INSERT INTO {$def->table} (" . str_comma_join($columns) . ") VALUES(" . str_comma_join($markers) . ")");
		
		if($stmt->execute(array_combine($markers, $values)))
		{
			// Return newly created Model if successful.
			return static::find(Database::lastInsertId());
		}
		else
		{
			// Error with create request.
			Database::error($stmt, "Could not create an instance of '" . $def->name() ."'");
		}

		return null;
	}


	public static function getProperties()
	{
		$ref = new ReflectionClass(new static());
		$props = $ref->getProperties(ReflectionProperty::IS_PUBLIC);

		$output = array();
		foreach($props as $prop) $output[] = $prop->getName();

		return $output;
	}


	public function set($data, $value = null)
	{
		$props = self::getProperties();

		if(is_array($data))
		{
			// Map values from an array.
			foreach($props as $prop)
			{
				if(array_key_exists($prop, $data) && !in_array($prop, $this->readonly))
				{
					// Pass value to model.
					$this->{$prop} = $data[$prop];
				}
			}
		}
		else
		{
			// Single value.
			if(in_array($data, $props))
			{
				$this->{$data} = $value;
			}
		}
	}


	public function toJSON()
	{
		$props = static::getProperties();
		$output = array();

		foreach($props as $prop)
		{
			$output[$prop] = $this->{$prop};
		}

		return json_encode($output, JSON_NUMERIC_CHECK);
	}


	public function name()
	{
		return get_class($this);
	}

}