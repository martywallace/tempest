<?php

namespace Tempest\Http;
use Tempest\Http\Middleware\MiddlewarePointer;

/**
 * An instance that contains its own set of middleware.
 *
 * @author Ascension Web Development
 */
interface HasMiddleware {

	/**
	 * Attach middleware.
	 *
	 * @param string $middleware The middleware class.
	 * @param string $method The method within the middleware class to call.
	 * @param array $options Options to provide to the middleware constructor.
	 *
	 * @return $this
	 */
	function addMiddleware(string $middleware, string $method = 'index', array $options = []);

	/**
	 * Get all attached middleware.
	 *
	 * @return MiddlewarePointer[]
	 */
	function getMiddleware(): array;

}