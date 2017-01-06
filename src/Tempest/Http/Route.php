<?php namespace Tempest\Http;

use Exception;
use Tempest\Tempest;
use Tempest\Utils\Memoizer;


/**
 * A single route definition.
 *
 * @property-read int $format The route format.
 *
 * @property-read string $uri The URI represented by this route.
 * @property-read string $method The request method used to access this route.
 * @property-read string[] $middleware Middleware to trigger before the handler is reached.
 * @property-read string $controller The controller responsible for this route.
 *
 * @package Tempest\Http
 * @author Marty Wallace
 */
final class Route extends Memoizer {

	/** An invalid definition format. */
	const FORMAT_INVALID = 0;

	/** A definition with the route URI and controller only. */
	const FORMAT_URI_CONTROLLER = 1;

	/** A definition with the route URI, method and controller. */
	const FORMAT_URI_METHOD_CONTROLLER = 2;

	/** A definition with the route URI, method, one or more middleware options and a controller. */
	const FORMAT_URI_METHOD_MIDDLEWARE_CONTROLLER = 3;

	/** @var array */
	private $_definition;

	/**
	 * Creates a route from a route definition array.
	 *
	 * @param array $definition The definition array.
	 *
	 * @throws Exception If the definition format was not valid.
	 */
	public function __construct(array $definition) {
		$this->_definition = $definition;

		if ($this->format === self::FORMAT_INVALID) {
			throw new Exception('Invalid route format.');
		}
	}

	public function __get($prop) {
		if ($prop === 'uri') {
			return $this->memoize('uri', function() {
				if ($this->_definition[0] === '/' && !empty(Tempest::get()->public)) {
					return Tempest::get()->public;
				}

				return Tempest::get()->public . '/' . trim($this->_definition[0], '/');
			});
		}

		if ($prop === 'method') {
			return $this->memoize('method', function() {
				if ($this->format === self::FORMAT_INVALID || $this->format === self::FORMAT_URI_CONTROLLER) {
					return 'GET';
				} else {
					return strtoupper($this->_definition[1]);
				}
			});
		}

		if ($prop === 'middleware') {
			return $this->memoize('middleware', function() {
				if ($this->format === self::FORMAT_URI_METHOD_MIDDLEWARE_CONTROLLER) return array_slice($this->_definition, 2, -1);
				else return [];
			});
		}

		if ($prop === 'controller') {
			return $this->memoize('controller', function() {
				return end($this->_definition);
			});
		}

		if ($prop === 'format') {
			return $this->memoize('format', function() {
				$dl = count($this->_definition);

				if ($dl < 2) return self::FORMAT_INVALID;
				if ($dl === 2) return self::FORMAT_URI_CONTROLLER;
				if ($dl === 3) return self::FORMAT_URI_METHOD_CONTROLLER;
				if ($dl > 3) return self::FORMAT_URI_METHOD_MIDDLEWARE_CONTROLLER;

				return self::FORMAT_INVALID;
			});
		}

		return null;
	}

}