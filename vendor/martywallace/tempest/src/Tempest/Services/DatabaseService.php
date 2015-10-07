<?php namespace Tempest\Services;

use RedBeanPHP\R;
use Exception;

/**
 * Provides methods for interacting with a database. RedBeanPHP is used as the underlying ORM.
 *
 * @property-read int $insertId The last insert ID value.
 * @property-read int $queryCount The total amount of executed queries.
 *
 * @package Tempest\Services
 * @author Marty Wallace
 */
class DatabaseService extends Service {

	public function __get($prop) {
		if ($prop === 'insertId') return R::getInsertID();
		if ($prop === 'totalQueries') return R::getQueryCount();

		return null;
	}

	protected function setup() {
		$config = app()->config('db');

		if (!empty($config)) {
			R::setup('mysql:host=' . $config['host'] . ';dbname=' . $config['name'], $config['user'], $config['pass']);

			if (!app()->dev) {
				// Freeze the database while not in development mode.
				R::freeze(true);
			}
		} else {
			throw new Exception('Database connection details are not defined.');
		}
	}

	public function create($name, array $fields = array()) {
		$model = R::dispense($name);

		if (!empty($fields)) {
			foreach ($fields as $field => $value) {
				$model->{$field} = $value;
			}
		}

		return $model;
	}

	public function save($models) {
		if (is_array($models)) {
			return R::storeAll($models);
		}

		return R::store($models);
	}

	public function find($name, $primary) {
		if (is_array($primary)) {
			return R::findAll($name, $primary);
		}

		return R::findOne($name, $primary);
	}

	public function query($query, $params = array()) {
		return R::exec($query, $params);
	}

	public function prop($query, $params = array()) {
		return R::getCell($query, $params);
	}

	public function row($query, $params = array()) {
		return R::getRow($query, $params);
	}

	public function rows($query, $params = array()) {
		return R::getAll($query, $params);
	}

}