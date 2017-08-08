<?php namespace Tempest\Http;

/**
 * A route to be handled by the HTTP kernel.
 *
 * @property-read string|string[] $method The HTTP method associated with this route.
 * @property-read string $uri The URI this route handles.
 *
 * @author Marty Wallace
 */
class Route {

	/** @var string|string[] */
	private $_method;

	/** @var string */
	private $_uri;

	/**
	 * Route constructor.
	 *
	 * @param string|string[] $method
	 * @param string $uri
	 */
	public function __construct($method, $uri) {
		$this->_method = $method;
		$this->_uri = $uri;
	}

	public function __get($prop) {
		if ($prop === 'method') return $this->method;
		if ($prop === 'uri') return $this->_uri;

		return null;
	}

}