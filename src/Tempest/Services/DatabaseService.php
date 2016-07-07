<?php namespace Tempest\Services;

use Exception;
use Tempest\Tempest;
use Spot\Config;
use Spot\Locator;
use Spot\Mapper;

/**
 * Provides methods for interacting with a database via the {@link https://github.com/vlucas/spot2 Spot ORM}.
 *
 * @package Tempest\Services
 * @author Marty Wallace
 */
class DatabaseService extends Service {

	/** @var Locator */
	private $_locator;

	protected function setup() {
		if (Tempest::get()->config->get('db')) {
			$config = new Config();
			$config->addConnection('mysql', Tempest::get()->config->get('db'));

			$this->_locator = new Locator($config);
		} else {
			throw new Exception('No database connection details have been defined.');
		}
	}

	/**
	 * Get a mapper via the internal {@link Locator}.
	 *
	 * @param string $name The model class name.
	 *
	 * @return Mapper
	 */
	public function mapper($name) {
		return $this->_locator->mapper($name);
	}

	/**
	 * Perform migration on a series of models.
	 *
	 * @param string|string[] $models One or more model names to migrate.
	 */
	public function migrate(array $models) {
		if (!is_array($models)) {
			$models = array($models);
		}
		
		foreach ($models as $model) {
			$this->mapper($model)->migrate();
		}
	}

}