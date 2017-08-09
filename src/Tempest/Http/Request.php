<?php namespace Tempest\Http;

use Exception;
use Tempest\Utility;

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

	/** @var mixed[] */
	private $_named = [];

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

	/**
	 * Attaches {@link Request::named() named} data to this request.
	 *
	 * @param string $property The property to create.
	 * @param mixed $value The value to attach.
	 *
	 * @throws Exception If the property already exists.
	 */
	public function attachNamed($property, $value) {
		if ($this->hasNamed($property)) throw new Exception('Named data "' . $property . '" has already been attached.');
		$this->_named[$property] = $value;
	}

	/**
	 * Determine whether named data exists.
	 *
	 * @param string $property The named property to check for.
	 *
	 * @return bool
	 */
	public function hasNamed($property) {
		return array_key_exists($property, $this->_named);
	}

	/**
	 * Retrieve named data.
	 *
	 * @param string $property The property to retrieve. If not provided, the entire set of named data is returned.
	 * @param mixed $fallback A fallback value to provide if the property did not exist.
	 *
	 * @return mixed
	 */
	public function named($property = null, $fallback = null) {
		if (empty($property)) return $this->_named;
		return Utility::dig($this->_named, $property, $fallback);
	}

}