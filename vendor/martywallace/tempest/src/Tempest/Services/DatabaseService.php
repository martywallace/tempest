<?php namespace Tempest\Services;

use RedBeanPHP\R;
use Tempest\Db\DbHelper;
use Tempest\Db\Model;
use Exception;

/**
 * Provides methods for interacting with a database. RedBeanPHP is used as the underlying ORM.
 *
 * @property-read int $insertId The last insert ID value.
 * @property-read int $queryCount The total amount of executed queries.
 * @property-read DbHelper $helper The internal database helper.
 *
 * @package Tempest\Services
 * @author Marty Wallace
 */
class DatabaseService extends Service {

	/** @var DbHelper */
	private $_helper;

	public function __get($prop) {
		if ($prop === 'insertId') return R::getInsertID();
		if ($prop === 'totalQueries') return R::getQueryCount();
		if ($prop === 'helper') return $this->_helper;

		return null;
	}

	protected function setup() {
		$config = app()->config('db');

		if (!empty($config)) {
			$this->_helper = new DbHelper();

			R::setup('mysql:host=' . $config['host'] . ';dbname=' . $config['name'], $config['user'], $config['pass']);
			R::getRedBean()->setBeanHelper($this->_helper);

			if (!app()->dev) {
				// Freeze the database while not in development mode.
				R::freeze(true);
			}
		} else {
			throw new Exception('Database connection details are not defined.');
		}
	}

	/**
	 * Creates a block of data that can be inserted into the database.
	 *
	 * @param string $type The data type, mapped to an appropriate table.
	 * @param array $fields Optional fields to fill in on the record upon creation.
	 * @param bool $mustHaveType Whether or not the type of record being created must be known by the application.
	 *
	 * @return Model
	 *
	 * @throws Exception
	 */
	public function create($type, array $fields = array(), $mustHaveType = true) {
		if ($mustHaveType && !$this->_helper->hasType($type)) {
			throw new Exception('Cannot create data type "' . $type . '" - no mapping was found for this type.');
		}

		$model = R::dispense($type);

		if (!empty($fields)) {
			foreach ($fields as $field => $value) {
				$model->{$field} = $value;
			}
		}

		return $model;
	}

	/**
	 * Saves model data into the database.
	 *
	 * @param Model[]|Model $models One or more models to save.
	 *
	 * @return array|int|string
	 */
	public function save($models) {
		if (is_array($models)) {
			return R::storeAll($models);
		}

		return R::store($models);
	}

	public function find($name, $primary) {
		if (is_array($primary)) {
			return R::loadAll($name, $primary);
		}

		return R::load($name, $primary);
	}

	public function trash($model) {
		R::trash($model);
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