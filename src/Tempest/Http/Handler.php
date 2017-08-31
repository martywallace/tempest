<?php namespace Tempest\Http;

use Exception;
use Tempest\Utility;

/**
 * A route handler - either in the form of middleware or a controller.
 *
 * @author Marty Wallace
 */
abstract class Handler {

	/**
	 * Creates a callable from a method within this class.
	 *
	 * @param string $method The class method to resolve to.
	 * @param array $options Optional options to attach to the handler when it is instantiated.
	 *
	 * @return array
	 *
	 * @throws Exception if the method does not exist within this class.
	 */
	public static function do($method = 'index', array $options = []) {
		if (!method_exists(static::class, $method)) {
			throw new Exception('HTTP handler "' . static::class . '" does not define a method "' . $method . '".');
		}

		return [static::class, $method, $options];
	}

	/** @var array */
	private $_options;

	/**
	 * Handler constructor.
	 *
	 * @internal
	 * @param array $options
	 */
	public function __construct(array $options = []) {
		$this->_options = $options;
	}

	public function __isset($prop) {
		return $this->{$prop} !== null;
	}

	/**
	 * Get an option provided to this handler.
	 *
	 * @param string $option The option to get. Returns the entire set of options if not provided.
	 * @param mixed $fallback A fallback value to provide if the option does not exist.
	 *
	 * @return mixed
	 */
	public function option($option = null, $fallback = null) {
		if (empty($option)) return $this->_options;
		return Utility::dig($this->_options, $option, $fallback);
	}

	/**
	 * Declare expected options. Any provided options that match the expected options will override their default
	 * values.
	 *
	 * @param array $expected The expected options and their default values.
	 */
	public function expect(array $expected) {
		$this->_options = array_merge($expected, $this->_options);
	}

}