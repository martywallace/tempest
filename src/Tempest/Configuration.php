<?php

namespace Tempest;

use Exception;
use Tempest\Utils\ObjectUtil;


/**
 * The Configuration class loads your application's configuration data.
 *
 * @package Tempest
 * @author Marty Wallace
 */
class Configuration {

	/** @var array */
	private $_data = [];

	/**
	 * Constructor.
	 *
	 * @param string $file The configuration file location relative to the application root.
	 *
	 * @throws Exception
	 */
	public function __construct($file) {
		if (is_file($file)) {
			/** @noinspection PhpIncludeInspection */
			$data = require($file);

			if (is_array($data)) $this->_data = $data;
			else throw new Exception('Configuration data must be an array.');
		} else {
			throw new Exception('Configuration file "' . $file . '" does not exist.');
		}
	}

	public function __get($prop) {
		return $this->get($prop);
	}

	public function __isset($prop) {
		return array_key_exists($prop, $this->_data);
	}

	/**
	 * Gets the value of the specified configuration data.
	 *
	 * @param string $prop The configuration data to get. Dot delimited values for sub-configuration data is allowed and
	 * passed through {@link ObjectUtil::getDeepValue()}.
	 * @param mixed $fallback A fallback value to use if the requested property does not exist.
	 *
	 * @return mixed
	 */
	public function get($prop = null, $fallback = null) {
		if ($prop === null) {
			// Return entire dataset on an argumentless call.
			return $fallback === null ? $this->_data : $fallback;
		}

		return ObjectUtil::getDeepValue($this->_data, $prop, $fallback);
	}

}