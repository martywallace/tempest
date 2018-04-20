<?php

namespace Tempest\Http;

use Tempest\Http\Middleware\MiddlewarePointer;

/**
 * A HTTP resource.
 *
 * @author Ascension Web Development
 */
abstract class Resource implements HasMiddleware {

	/** @var string */
	private $uri = '';

	/** @var MiddlewarePointer[] */
	private $middleware = [];

	/**
	 * Uri constructor.
	 *
	 * @param string $uri
	 */
	public function __construct(string $uri) {
		$this->uri = $uri;
	}

	/**
	 * The URI that this resource handles.
	 *
	 * @return string
	 */
	public function getUri(): string {
		return $this->uri;
	}

	/**
	 * Prepend a value to the URI.
	 *
	 * @param string $value The value to prepend.
	 *
	 * @return self
	 */
	public function prependUri(string $value): self {
		$this->uri = '/' . trim(trim($value, '/\\') . $this->uri, '/\\');
		return $this;
	}

	/**
	 * Prepend middleware to this resource.
	 *
	 * @param array $action The middleware action to prepend.
	 */
	public function prependMiddleware($action): void {
		array_unshift($this->middleware, $action);
	}

	public function addMiddleware(string $middleware, string $method = 'index', array $options = []) {
		$this->middleware[] = new MiddlewarePointer($middleware, $method, $options);

		return $this;
	}

	public function getMiddleware(): array {
		return $this->middleware;
	}

}