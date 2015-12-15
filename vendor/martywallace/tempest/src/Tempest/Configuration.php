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
	private $_data;

	/**
	 * Constructor.
	 *
	 * @param string $file The configuration file location relative to the application root.
	 * @param string $server The SERVER_NAME to cascade global configuration with. If not provided, this defaults to:
	 * <code>$_SERVER['SERVER_NAME']</code>
	 *
	 * @throws Exception
	 */
	public function __construct($file, $server = null) {
		$file = $file . '.php';

		$server = $server === null ? $_SERVER['SERVER_NAME'] : $server;
		$server = preg_replace('/^www\./', '', $server);

		if (is_file($file)) {
			$data = require($file);

			if (is_array($data)) {
				if (array_key_exists('*', $data)) {
					$this->_data = $data['*'];

					if (array_key_exists($server, $data)) {
						// Cascade data.
						$this->_data = array_replace_recursive($this->_data, $data[$server]);
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