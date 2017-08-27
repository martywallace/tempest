<?php namespace Tempest\Http;

use Tempest\Utility;

/**
 * A request made to the HTTP kernel.
 *
 * @author Marty Wallace
 */
class Request implements Message {

	/**
	 * Capture an incoming HTTP request and generate a new {@link Request request} from it.
	 *
	 * @return static
	 */
	public static function capture() {
		$extras = [
			'ip' => isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : null,
			'https' => isset($_SERVER['HTTPS']) && !empty($_SERVER['HTTPS']),
		];

		return new static(
			$_SERVER['REQUEST_METHOD'],
			$_SERVER['REQUEST_URI'],
			getallheaders(),
			file_get_contents('php://input'),
			$_COOKIE,
			$extras
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

	/** @var mixed[] */
	private $_cookies = [];

	/** @var array */
	private $_extra = [];

	/**
	 * Request constructor.
	 *
	 * @param string $method The request method e.g. GET, POST.
	 * @param string $uri The request URI, including optional querystring.
	 * @param array $headers The request headers.
	 * @param string $body The request body.
	 * @param array $cookies Cookies attached to the request.
	 * @param array $extra Additional request information like IP address.
	 */
	public function __construct($method, $uri, array $headers = [], $body = '', array $cookies = [], array $extra = []) {
		$this->_method = strtoupper($method);
		$this->_uri = parse_url($uri, PHP_URL_PATH);
		$this->_body = $body;
		$this->_cookies = $cookies;
		$this->_extra = $extra;

		if (!empty($headers)) {
			foreach ($headers as $key => $value) {
				$this->_headers[Utility::kebab($key, true)] = $value;
			}
		}

		// Populate querystring array.
		parse_str(parse_url($uri, PHP_URL_QUERY), $this->_query);
	}

	/**
	 * Returns the request body.
	 *
	 * @return string
	 */
	public function getBody() {
		return $this->_body;
	}

	/**
	 * Returns the request headers.
	 *
	 * @return string
	 */
	public function getHeaders() {
		return $this->_headers;
	}

	/**
	 * Get the HTTP request method.
	 *
	 * @return string
	 */
	public function getMethod() {
		return $this->_method;
	}

	/**
	 * Get the IP address that the request originated from.
	 *
	 * @return string
	 */
	public function getIP() {
		return $this->extra('ip');
	}

	/**
	 * Whether or not this request was made over HTTPS.
	 *
	 * @return bool
	 */
	public function isHttps() {
		return $this->extra('https', false);
	}

	/**
	 * Get the request URI.
	 *
	 * @return string
	 */
	public function getUri() {
		return $this->_uri;
	}

	/**
	 * Attaches {@link Request::named() named} data to this request.
	 *
	 * @param string $property The property to create.
	 * @param mixed $value The value to attach.
	 */
	public function attachNamed($property, $value) {
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
	 */
	public function attachData($property, $value) {
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
		return array_key_exists(Utility::kebab($header, true), $this->_headers);
	}

	/**
	 * Returns a request header.
	 *
	 * @param string $header The request header to retrieve. If not provided, returns the {@link getHeaders() entire}
	 * set of headers.
	 * @param mixed $fallback The fallback value to provide if the header does not exist.
	 *
	 * @return string
	 */
	public function header($header = null, $fallback = null) {
		if (empty($header)) return $this->getHeaders();
		return Utility::dig($this->_headers, Utility::kebab($header, true), $fallback);
	}

	/**
	 * Get a cookie from the request.
	 *
	 * @param string $cookie The name of the cookie. If not provided, returns all cookies.
	 * @param mixed $fallback A fallback value to provide if the cookie does not exist.
	 *
	 * @return mixed
	 */
	public function cookie($cookie = null, $fallback = null) {
		if (empty($cookie)) return $this->_cookies;
		return Utility::dig($this->_cookies, $cookie, $fallback);
	}

	/**
	 * Retrieve data from the request extras.
	 *
	 * @param string $prop The property to retrieve.
	 * @param mixed $fallback A fallback value to use if the property does not exist.
	 *
	 * @return mixed
	 */
	protected function extra($prop, $fallback = null) {
		return Utility::dig($this->_extra, $prop, $fallback);
	}

}