<?php namespace Tempest\Http;

/**
 * A HTTP resource.
 *
 * @author Marty Wallace
 */
abstract class Resource {

	/** @var string */
	private $_uri = '';

	/** @var mixed[][] */
	private $_middleware = [];

	/**
	 * Uri constructor.
	 *
	 * @param string $uri
	 */
	public function __construct($uri) {
		$this->_uri = $uri;
	}

	/**
	 * The URI that this resource handles.
	 *
	 * @return string
	 */
	public function getUri() {
		return $this->_uri;
	}

	/**
	 * Prepend a value to the URI.
	 *
	 * @param string $value The value to prepend.
	 *
	 * @return $this
	 */
	public function prependUri($value) {
		$this->_uri = '/' . trim(trim($value, '/\\') . $this->_uri, '/\\');
		return $this;
	}

	/**
	 * Prepend middleware to this resource.
	 *
	 * @param array $action The middleware action to prepend.
	 */
	public function prependMiddleware($action) {
		array_unshift($this->_middleware, $action);
	}

	/**
	 * Attach one or more middleware to this resources.
	 *
	 * @param array[] ...$actions The middleware actions.
	 *
	 * @return $this
	 */
	public function middleware(...$actions) {
		$this->_middleware = array_merge($this->_middleware, $actions);
		return $this;
	}

	/**
	 * Get all registered middleware.
	 *
	 * @return mixed[][]
	 */
	public function getMiddleware() {
		return $this->_middleware;
	}

}