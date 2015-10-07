<?php namespace Tempest\Db;

use RedBeanPHP\BeanHelper\SimpleFacadeBeanHelper;
use RedBeanPHP\OODBBean;
use Exception;

class DbHelper extends SimpleFacadeBeanHelper {

	/** @var string[] */
	private $_models = array();

	public function getModelForBean(OODBBean $bean) {
		$type = $bean->getMeta('type');

		if (array_key_exists($type, $this->_models)) {
			$class = $this->_models[$type];

			if (class_exists($class)) {
				$model = new $this->_models[$type]();

				if ($model instanceof Model) {
					$model->loadBean($bean);

					return $model;
				} else {
					throw new Exception('Model "' . get_class($model) . '" must subclass "Tempest\Db\Model".');
				}
			} else {
				throw new Exception('No such model "' . $class . '".');
			}
		}

		return null;
	}

	public function map(array $models) {
		if (!empty($models)) {
			$this->_models = $models;
		}
	}

	public function hasType($type) {
		return array_key_exists($type, $this->_models);
	}

}