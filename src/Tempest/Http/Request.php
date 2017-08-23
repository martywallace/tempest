<?php namespace Tempest\Http;

use Exception;
use Tempest\Utility;

/**
 * A request made to the HTTP kernel.
 *
 * @property-read string $method The request method.
 * @property-read string $uri The request URI.
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
			getallheaders(),
			file_get_contents('php://input')
		);
	}

	/** @var string */
	private $_method;

	/** @var string */
	private $_uri;

	/** @var array */
	private $_query;

	/** @var array */
	private $_headers = [];

	/** @var string */
	private $_body;

	/** @var mixed[] */
	private $_named = [];

	/** @var mixed[] */
	private $_data = [];

	/**
	 * Request constructor.
	 *
	 * @param string $method The request method e.g. GET, POST.
	 * @param string $uri The request URI, including optional querystring.
	 * @param array $headers The request headers.
	 * @param string $body The request body.
	 */
	public function __construct($method, $uri, array $headers = [], $body = '') {
		$this->_method = strtoupper($method);
		$this->_uri = parse_url($uri, PHP_URL_PATH);
		$this->_body = $body;

		if (!empty($headers)) {
			foreach ($headers as $key => $value) {
				$this->_headers[strtolower($key)] = $value;
			}
		}

		// Populate querystring array.
		parse_str(parse_url($uri, PHP_URL_QUERY), $this->_query);
	}

	public function __get($prop) {
		if ($prop === 'method') return $this->_method;
		if ($prop === 'uri') return $this->_uri;
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

	/**
	 * Attaches {@link Request::data() data} to this request.
	 *
	 * @param string $property The property to create.
	 * @param mixed $value The value to attach.
	 *
	 * @throws Exception If the property already exists.
	 */
	public function attachData($property, $value) {
		if ($this->hasData($property)) throw new Exception('Data "' . $property . '" has already been attached.');
		$this->_data[$property] = $value;
	}

	/**
	 * Determine whether data exists.
	 *
	 * @param string $property The property to check for.
	 *
	 * @return bool
	 */
	public function hasData($property) {
		return array_key_exists($property, $this->_data);
	}

	/**
	 * Retrieve data.
	 *
	 * @param string $property The property to retrieve. If not provided, the entire set of data is returned.
	 * @param mixed $fallback A fallback value to provide if the property did not exist.
	 *
	 * @return mixed
	 */
	public function data($property = null, $fallback = null) {
		if (empty($property)) return $this->_data;
		return Utility::dig($this->_data, $property, $fallback);
	}

	/**
	 * Determine whether a field exists in the request querystring.
	 *
	 * @param string $property The property to check for.
	 *
	 * @return bool
	 */
	public function hasQuery($property) {
		return array_key_exists($property, $this->_query);
	}

	/**
	 * Retrieve querystring data.
	 *
	 * @param string $property The property to retrieve. If not provided, the entire query set is returned.
	 * @param mixed $fallback A fallback value to provide if the querystring does not contain the property.
	 *
	 * @return mixed
	 */
	public function query($property = null, $fallback = null) {
		if (empty($property)) return $this->_query;
		return Utility::dig($this->_query, $property, $fallback);
	}

	/**
	 * Determine whether a request header exists.
	 *
	 * @param string $header The request header to check for. Case-insensitive.
	 *
	 * @return bool
	 */
	public function hasHeader($header) {
		return array_key_exists(strtolower($header), $this->_headers);
	}

	/**
	 * Returns a request header.
	 *
	 * @param string $header The request header to retrieve. If not provided, returns the entire set of headers.
	 * @param mixed $fallback The fallback value to provide if the header does not exist.
	 *
	 * @return string
	 */
	public function header($header = null, $fallback = null) {
		if (empty($header)) return $this->_headers;
		return Utility::dig($this->_headers, strtolower($header), $fallback);
	}

}