<?php namespace Tempest\Http;

/**
 * A request made to the HTTP kernel.
 *
 * @property-read string $method The request method.
 * @property-read string $uri The request URI.
 * @property-read array $headers The request headers.
 * @property-read string $body The request body.
 *
 * @author Marty Wallace
 */
class Request extends Message {

	/**
	 * Capture an incoming HTTP request and generate a new {@link Request request} from it.
	 *
	 * @return static
	 */
	public static function capture() {
		return new static(
			$_SERVER['REQUEST_METHOD'],
			$_SERVER['REQUEST_URI'],
			[],
			file_get_contents('php://input')
		);
	}

	/** @var string */
	private $_method;

	/** @var string */
	private $_uri;

	/** @var array */
	private $_headers;

	/** @var string */
	private $_body;

	/**
	 * Request constructor.
	 *
	 * @param string $method
	 * @param string $uri
	 * @param array $headers
	 * @param string $body
	 */
	public function __construct($method, $uri, array $headers = [], $body = '') {
		$this->_method = strtoupper($method);
		$this->_uri = $uri;
		$this->_headers = $headers;
		$this->_body = $body;
	}

	public function __get($prop) {
		if ($prop === 'method') return $this->_method;
		if ($prop === 'uri') return $this->_uri;
		if ($prop === 'headers') return $this->_headers;
		if ($prop === 'body') return $this->_body;

		return null;
	}

}