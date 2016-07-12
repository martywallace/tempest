<?php namespace Tempest\Models;


/**
 * A model stored in the database.
 *
 * @package Tempest\Models
 * @author Marty Wallace
 */
abstract class DbModel extends Model {

	/**
	 * Creates a new instance of this model.
	 *
	 * @param array $data Data to fill the new model with.
	 *
	 * @return static
	 */
	public static function create(array $data = array()) {
		$instance = new static();

		foreach ($data as $field => $value) {
			if (property_exists($instance, $field)) {
				$instance[$field] = $value;
			}
		}

		return $instance;
	}

	/**
	 * Store changes to this model in the database.
	 *
	 * @return bool
	 */
	public abstract function save();

}