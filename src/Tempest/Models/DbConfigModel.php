<?php namespace Tempest\Models;

/**
 * A piece of database level configuration data for the application.
 *
 * @property string $name The name used to refer to this configuration data.
 * @property string $env The environment the value is related to.
 * @property string $value The value itself.
 *
 * @package Tempest\Models
 * @author Marty Wallace
 */
class DbConfigModel extends DbModel {

	protected static $table = 'config';

	public static function fields() {
		return array(
			'name' => array('type' => self::FIELD_STRING, 'primary' => 'primary'),
			'env' => array('type' => self::FIELD_STRING, 'primary' => 'primary'),
			'value' => array('type' => self::FIELD_STRING)
		);
	}

}