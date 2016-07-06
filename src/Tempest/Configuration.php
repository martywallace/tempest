<?php

namespace Tempest;

use Exception;


/**
 * The Configuration class loads cascading configuration data.
 *
 * @package Tempest
 * @author Marty Wallace
 */
class Configuration {

	/** @var array */
	private $_data = array();

	/**
	 * Constructor.
	 *
	 * @param string $file The configuration file location relative to the application root.
	 *
	 * @throws Exception
	 */
	public function __construct($file) {
		$file = $file . '.php';
		$env = Environment::current();

		if (is_file($file)) {
			/** @noinspection PhpIncludeInspection */
			$data = require($file);

			if (is_array($data)) {
				if (array_key_exists(Environment::ALL, $data)) {
					$this->_data = $data[Environment::ALL];

					if (array_key_exists($env, $data)) {
						$this->_data = array_replace_recursive($this->_data, $data[$env]);
					}
				} else {
					$this->_data = $data;
				}
			} else {
				throw new Exception('Configuration data must be an array.');
			}
		} else {
			throw new Exception('Configuration file "' . $file . '" does not exist.');
		}
	}

	public function __get($prop) {
		return $this->get($prop);
	}

	/**
	 * Gets the value of the specified configuration data.
	 *
	 * @param string $prop The configuration data to get.
	 * @param mixed $fallback A fallback value to use if the requested property does not exist.
	 *
	 * @return mixed
	 */
	public function get($prop = null, $fallback = null) {
		if ($prop === null) {
			// Return entire dataset on an argumentless call.
			return $fallback === null ? $this->_data : $fallback;
		}

		return array_key_exists($prop, $this->_data) ? $this->_data[$prop] : $fallback;
	}

}