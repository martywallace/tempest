<?php namespace Tempest\Http;

use Exception;
use JsonSerializable;
use Tempest\Tempest;

/**
 * A controller or middleware action.
 *
 * @property-read string $class The class associated with this action.
 * @property-read string $method The method attached to the class that will be called.
 * @property-read mixed[] $meta Meta information attached to the action.
 * @property-read mixed[] $args Arguments to be provided to the class method when called.
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
	private $_meta = [];

	/** @var array */
	private $_args = [];

	public function __construct($class, $method, array $meta = []) {
		$this->_class = $class;
		$this->_method = $method;
		$this->_meta = $meta;

		if (!method_exists($class, $method)) {
			throw new Exception('Action "' . $method . '" does not exist on "' . $class . '".');
		}
	}

	public function __get($prop) {
		if ($prop === 'class') return $this->_class;
		if ($prop === 'method') return $this->_method;
		if ($prop === 'args') return $this->_args;

		return null;
	}

	/**
	 * Binds a new set of arguments provided to this method to the arguments provided to the action when calling
	 * {@link execute()}.
	 *
	 * @param mixed[] ...$args Arguments to bind.
	 *
	 * @return $this
	 */
	public function bind(...$args) {
		$this->_args = $args;
		return $this;
	}

	/**
	 * Prepend values to the list of bound arguments.
	 *
	 * @param mixed[] ...$args Arguments to prepend.
	 *
	 * @return $this
	 */
	public function bindBefore(...$args) {
		$this->_args = array_merge($args, $this->_args);
		return $this;
	}

	/**
	 * Append values to the list of bound arguments.
	 *
	 * @param mixed[] ...$args Arguments to append.
	 *
	 * @return $this
	 */
	public function bindAfter(...$args) {
		$this->_args = array_merge($this->_args, $args);
		return $this;
	}

	/**
	 * Execute the action. Any arguments that were provided to {@link bind()}, {@link bindBefore()} and
	 * {@link bindAfter()} are forwarded to the action. If this method is called with its own arguments, those arguments
	 * are used instead.
	 *
	 * @param mixed[] ...$args Arguments to bind. Providing a value here will overwrite any previously bound values.
	 *
	 * @return mixed
	 */
	public function execute(...$args) {
		return call_user_func_array([
			Tempest::get()->memoization->cache(static::class, $this->_class, new $this->_class()),
			$this->_method
		], array_merge(empty($args) ? $this->_args : $args, [$this->_meta]));
	}

	public function jsonSerialize() {
		return [
			'class' => $this->_class,
			'method' => $this->_method,
			'meta' => $this->_meta,
			'args' => $this->_args
		];
	}

}