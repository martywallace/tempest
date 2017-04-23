<?php namespace Tempest\Http;

use Exception;
use JsonSerializable;
use Tempest\Tempest;

/**
 * A controller or middleware action.
 *
 * @package Tempest\Http
 * @author Marty Wallace
 */
class Action implements JsonSerializable {

	/** @var string */
	private $_class;

	/** @var string */
	private $_method;

	/** @var array */
	private $_args = [];

	public function __construct($class, $method) {
		$this->_class = $class;
		$this->_method = $method;

		if (!method_exists($class, $method)) {
			throw new Exception('Action "' . $method . '" does not exist on "' . $class . '".');
		}
	}

	public function __get($prop) {
		if ($prop === 'class') return $this->_class;
		if ($prop === 'method') return $this->_method;

		return null;
	}

	/**
	 * Binds arguments provided to this method to the arguments provided to the action when calling {@link execute()}.
	 *
	 * @return $this
	 */
	public function bind() {
		$this->_args = func_get_args();
		return $this;
	}

	/**
	 * Execute the action. Any arguments that were provided to {@link bind()} are forwarded to the action. If this
	 * method is called with its own arguments, those arguments are used instead.
	 *
	 * @return mixed
	 */
	public function execute() {
		$instance = Tempest::get()->memoization->cache(static::class, $this->_class, new $this->_class());
		return call_user_func_array([$instance, $this->_method], func_num_args() === 0 ? $this->_args : func_get_args());
	}

	public function jsonSerialize() {
		return [
			'class' => $this->_class,
			'method' => $this->_method,
			'args' => $this->_args
		];
	}

}