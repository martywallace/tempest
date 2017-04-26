<?php namespace Tempest\Services;

use Tempest\Tempest;
use Tempest\Utils\JSONUtil;

/**
 * A service that provides memoization to the application, used to store and provide the result of expensive method
 * calls, especially database queries.
 *
 * @package Tempest\Services
 * @author Marty Wallace
 */
class MemoizeService {

	/** @var array[] */
	private $_cache = [];

	/**
	 * Retrieve a value from the cache. If the value does not exists, a provided value is stored for future calls.
	 *
	 * @param string $source The source of the call to memoize data. Usually the name of the class calling the method.
	 * @param string|array $key A unique key within the source to store the data against. If the key is an array, the
	 * {@link createKey()} method is used to generate a unique key by flattening and hashing the array content.
	 * @param mixed|callable $data Data or a function that returns data to be stored.
	 *
	 * @return mixed
	 */
	public function cache($source, $key, $data) {
		if (is_array($key)) $key = $this->createKey($key);

		if (!array_key_exists($source, $this->_cache)) $this->_cache[$source] = [];
		if (!array_key_exists($key, $this->_cache[$source])) $this->_cache[$source][$key] = is_callable($data) ? $data() : $data;

		return $this->_cache[$source][$key];
	}

	/**
	 * Remove existing data from the cache, causing the next call to {@link cache()} to fetch new data.
	 *
	 * @param string $source The source to remove data for. If this is NULL, the entire cache is destroyed.
	 * @param string|array $key The key within the source to remove data from. If this is NULL, the entire set of data
	 * under $source will be removed.
	 */
	public function forget($source = null, $key = null) {
		if ($source === null) $this->_cache = [];
		if ($key === null) unset($this->_cache[$source]);

		else {
			if (is_array($key)) $key = $this->createKey($key);
			unset($this->_cache[$source][$key]);
		}
	}

	/**
	 * Create a unique key from an array of data.
	 *
	 * @param array $data The input data.
	 *
	 * @return string
	 */
	public function createKey(array $data) {
		return sha1(JSONUtil::encode($data));
	}

	/**
	 * Dump the contents of the cache foe debugging.
	 *
	 * @param string $format The output format.
	 *
	 * @see Tempest::DUMP_VAR_DUMP
	 * @see Tempest::DUMP_PRINT_R
	 * @see Tempest::DUMP_JSON
	 */
	public function dump($format = Tempest::DUMP_PRINT_R) {
		Tempest::get()->dump($this->_cache, $format);
	}

}