<?php namespace Tempest\Utils;

/**
 * The Memoizer provides utility methods for storing expensive object creation or fetching (especially database calls)
 * against keys in an internal storage.
 *
 * @package Tempest\Utils
 * @author Marty Wallace
 */
abstract class Memoizer {

	/** @var mixed[] */
	private $_memoized = array();

	public function __isset($prop) {
		return property_exists($this, $prop) ||
			array_key_exists($prop, $this->_memoized) ||
			$this->{$prop} !== null;
	}

	/**
	 * Memoize a value. The first time this function is called against a key, the value will be added to the internal
	 * list of values that are memoized. On subsequent calls, the memoized value is returned.
	 *
	 * @param string $key The key used to store and fetch the memoized value.
	 * @param callable|mixed $value The value to memoize. If the value is a callable, use the return value of that
	 * callable (this is the most likely scenario, where an anonymous function is used).
	 *
	 * @return mixed
	 */
	protected function memoize($key, $value) {
		if (!$this->isMemoized($key)) {
			$this->_memoized[$key] = is_callable($value) ? $value() : $value;
		}

		return $this->_memoized[$key];
	}

	/**
	 * Invalidate a memoized value stored against a given key, forcing a new value to be fetched the next time access to
	 * it is attempted.
	 *
	 * @param string $key The key. If a key is not provided, invalidate the entire stack of memoized data.
	 */
	protected function unmemoize($key = null) {
		if ($key !== null) {
			unset($this->_memoized[$key]);
		} else {
			$this->_memoized = array();
		}
	}

	/**
	 * Determine whether a value for a given key exists in the list of memoized values.
	 *
	 * @param string $key The key to check.
	 *
	 * @return bool
	 */
	protected function isMemoized($key) {
		return array_key_exists($key, $this->_memoized);
	}

}