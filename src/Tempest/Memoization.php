<?php namespace Tempest;

use Closure;
use ReflectionFunction;

/**
 * Provides memoization functionality.
 *
 * @author Marty Wallace
 */
class Memoization {

	/** @var array */
	private static $_cache = [];

	/**
	 * Cache the return value of a closure and return that value on repeat calls.
	 *
	 * @param Closure $closure The closure to call.
	 * @param string $key A key to store the value against. If not provided, one is generated based on the function call
	 * that requested memoization using {@link debug_backtrace()}.
	 *
	 * @return mixed
	 */
	public static function cache(Closure $closure, $key = null) {
		$backtrace = debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT, 2);
		$function = new ReflectionFunction($closure);

		if (empty($key)) {
			if (count($backtrace) >= 2) {
				$key = get_class($function->getClosureThis()) . '@' . Utility::dig($backtrace, '1.function') . '@' . serialize($function->getStaticVariables());
			}
		}

		if (!empty($key)) {
			if (!array_key_exists($key, static::$_cache)) {
				static::$_cache[$key] = $closure();
			}

			return static::$_cache[$key];
		}

		// Fall back to always calling the closure.
		return $closure();
	}

	/**
	 * Deletes an item from the cache, causing the next call to {@link cache()} to re-run the closure it was provided.
	 *
	 * @param string $key The cache key.
	 */
	public static function uncache($key) {
		unset(static::$_cache[$key]);
	}

	/**
	 * Gets the current memoization cache.
	 *
	 * @return array
	 */
	public static function getCache() {
		return static::$_cache;
	}

}