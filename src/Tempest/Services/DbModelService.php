<?php namespace Tempest\Services;

use Tempest\Tempest;
use Tempest\Models\DbModel;

/**
 * A service that interacts with a specific collection of models.
 *
 * @see Tempest\Models\DbModel
 *
 * @package Tempest\Services
 * @author Marty Wallace
 */
abstract class DbModelService extends Service {

	/**
	 * Defines the model type that this ModelService interacts with.
	 *
	 * @return string
	 */
	protected abstract function defineModel();

	/**
	 * Finds and returns a DbModel.
	 *
	 * @param string|int $primary The primary key value used to find the model.
	 *
	 * @return DbModel
	 */
	public function find($primary) {
		return $this->memoize('__' . $primary, function() use ($primary) {
			return Tempest::get()->db->one('SELECT * FROM ' . $this->getTable() . ' WHERE ' . $this->getPrimary() . ' = ?', array($primary), $this->defineModel());
		});
	}

	/**
	 * Find and return all DbModels.
	 *
	 * @return DbModel[]
	 */
	public function all() {
		return $this->memoize('all', function() {
			return Tempest::get()->db->all('SELECT * FROM ' . $this->getTable() . ' ORDER BY ' . $this->getPrimary(), null, $this->defineModel());
		});
	}

	/**
	 * Delete a DbModel with the specified primary key value.
	 *
	 * @param string|number $primary The primary key value.
	 */
	public function delete($primary) {
		Tempest::get()->db->query('DELETE FROM ' . $this->getTable() . ' WHERE ' . $this->getPrimary() . ' = ?', array($primary));
	}

	/**
	 * The name of the table that holds the models this service deals with.
	 *
	 * @return string
	 */
	public function getTable() {
		return $this->memoize('__table', call_user_func($this->defineModel() . '::getTable'));
	}

	/**
	 * The primary key of the table that holds the models this service deals with.
	 *
	 * @return string
	 */
	public function getPrimary() {
		return $this->memoize('__primary', call_user_func($this->defineModel() . '::getPrimary'));
	}

}