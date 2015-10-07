<?php namespace Tempest\Db;

use RedBeanPHP\SimpleModel;
use RedBeanPHP\OODBBean;

/**
 * The model class allows you to attach functionality to data returned from the database.
 *
 * @property-read int $id The model ID.
 * @property-read OODBBean $record The underlying data record.
 *
 * @package Tempest\Db
 * @author Marty Wallace
 */
abstract class Model extends SimpleModel {

	public function __get($prop) {
		if ($prop === 'id') return $this->bean->getID();
		if ($prop === 'record') return $this->bean;

		return parent::__get($prop);
	}

}