<?php namespace Tempest\Events;

use Exception;
use Symfony\Component\EventDispatcher\Event;
use Tempest\Database\Model;

/**
 * An event dispatched by a model.
 *
 * @author Marty Wallace
 */
class ModelEvent extends Event {

	/**
	 * Emitted when a model is {@link Model::reset reset}.
	 */
	const RESET = 'model.reset';

	/**
	 * Emitted before a model is {@link Model::save saved}.
	 */
	const BEFORE_SAVE = 'model.before-save';

	/**
	 * Emitted after a model is {@link Model::save saved}.
	 */
	const AFTER_SAVE = 'model.after-save';

	/** @var Exception */
	private $_model;

	public function __construct(Model $model) {
		$this->_model = $model;
	}

	/**
	 * @return Model
	 */
	public function getModel() {
		return $this->_model;
	}

}