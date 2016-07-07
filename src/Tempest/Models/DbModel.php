<?php namespace Tempest\Models;

use Spot\Entity;


/**
 * A model stored in the database. This class is a very thin wrapper for Spot's {@link Entity} class.
 *
 * @package Tempest\Models
 * @author Marty Wallace
 */
abstract class DbModel extends Entity {

	const FIELD_SMALLINT = 'smallint';
	const FIELD_INT = 'integer';
	const FIELD_BIGINT = 'bigint';
	const FIELD_DECIMAL = 'decimal';
	const FIELD_FLOAT = 'float';
	const FIELD_STRING = 'string';
	const FIELD_TEXT = 'text';
	const FIELD_GUID = 'guid';
	const FIELD_BINARY = 'binary';
	const FIELD_BLOB = 'blob';
	const FIELD_BOOLEAN = 'boolean';
	const FIELD_DATE = 'date';
	const FIELD_DATETIME = 'datetime';
	const FIELD_DATETIMEZ = 'datetimez';
	const FIELD_TIME = 'time';
	const FIELD_ARRAY = 'array';
	const FIELD_SIMPLEARRAY = 'simple_array';
	const FIELD_JSON = 'json_array';
	const FIELD_OBJECT = 'object';

}