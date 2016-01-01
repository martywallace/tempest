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

		$serverHost = $_SERVER['SERVER_NAME'];
		$serverPort = intval($_SERVER['SERVER_PORT']);

		if (is_file($file)) {
			/** @noinspection PhpIncludeInspection */
			$data = require($file);

			if (is_array($data)) {
				if (array_key_exists('*', $data)) {
					$this->_data = $data['*'];

					foreach ($data as $hosts => $block) {
						$hosts = preg_split('/,\s*/', $hosts);

						foreach ($hosts as $host) {
							$port = parse_url($host, PHP_URL_PORT);

							if ((empty($port) && $serverHost === $host) || ($serverHost . ':' . $serverPort === $host)) {
								$this->_data = array_replace_recursive($this->_data, $block);
								break;
							}
						}
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