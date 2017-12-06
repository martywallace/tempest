<?php namespace Tempest\Http;

use Closure;
use Tempest\Kernel\Input;
use Tempest\Services\SessionService;
use Tempest\Utility;
use Tempest\Database\Models\User;
use Tempest\Validation\Validator;
use Negotiation\AcceptCharset;
use Negotiation\AcceptEncoding;
use Negotiation\AcceptHeader;
use Negotiation\CharsetNegotiator;
use Negotiation\EncodingNegotiator;
use Negotiation\Negotiator;
use Negotiation\LanguageNegotiator;
use Negotiation\Accept;
use Negotiation\AcceptLanguage;

/**
 * A request made to the HTTP kernel.
 *
 * @author Marty Wallace
 */
class Request extends Message implements Input {

	/**
	 * Capture an incoming HTTP request and generate a new {@link Request request} from it.
	 *
	 * @return static
	 */
	public static function capture() {
		$extras = [
			'ip' => isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : null,
			'https' => isset($_SERVER['HTTPS']) && !empty($_SERVER['HTTPS']),
			'port' => isset($_SERVER['SERVER_PORT']) ? $_SERVER['SERVER_PORT'] : null
		];

		if (function_exists('getallheaders')) {
			$headers = getallheaders();
		} else {
			// Nginx environments do not have the inbuilt getallheaders() function. This polyfill
			// was taken from http://php.net/getallheaders
			$headers = [];

			if (is_array($_SERVER)) {
				foreach ($_SERVER as $name => $value) {
					if (substr($name, 0, 5) === 'HTTP_') {
						$key = str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))));
						$headers[$key] = $value;
					}
				}
			}
		}

		return new static(
			$_SERVER['REQUEST_METHOD'],
			$_SERVER['REQUEST_URI'],
			$headers,
			file_get_contents('php://input'),
			$_COOKIE,
			$extras
		);
	}

	/**
	 * Statically create a new request object.
	 *
	 * @param string $method The request method.
	 * @param string $uri The request URI.
	 * @param array $headers The request headers.
	 * @param string $body The request body.
	 * @param array $cookies Cookies attached to the request.
	 * @param array $extra Additional request information.
	 *
	 * @return static
	 */
	public static function make($method, $uri, array $headers = [], $body = '', array $cookies = [], array $extra = []) {
		return new static($method, $uri, $headers, $body, $cookies, $extra);
	}

	/** @var string */
	private $_method;

	/** @var string */
	private $_uri;

	/** @var array */
	private $_query;

	/** @var mixed[] */
	private $_params = [];

	/** @var mixed[] */
	private $_data = [];

	/** @var mixed[] */
	private $_cookies = [];

	/** @var array */
	private $_extra = [];

	/** @var Negotiator[] */
	private $_negotiators = [];

	/** @var User */
	private $_user;

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
	private function __construct($method, $uri, array $headers = [], $body = '', array $cookies = [], array $extra = []) {
		$this->setHeaders($headers);
		$this->setBody($body);

		$this->_method = strtoupper($method);
		$this->_uri = parse_url($uri, PHP_URL_PATH);
		$this->_cookies = $cookies;
		$this->_extra = $extra;

		// Populate querystring array.
		parse_str(parse_url($uri, PHP_URL_QUERY), $this->_query);
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
	 * The hostname that the request was made to.
	 *
	 * @return string
	 */
	public function getHost() {
		return $this->getHeader(Header::HOST);
	}

	/**
	 * Get the port that the request was made to. Typically returns 80 for HTTP requests and 443 for HTTPS.
	 *
	 * @return int
	 */
	public function getPort() {
		return intval($this->extra('port'));
	}

	/**
	 * Gets the base application URL e.g. "https://mydomain.com". If the request was made over HTTP and the port is not
	 * 80, or the request was made over HTTPS and the port is not 443; the port will be included in the result.
	 *
	 * @return string
	 */
	public function getBaseUrl() {
		return ($this->isHttps() ? 'https' : 'http') . '://' . $this->getHost() . (
			($this->isHttps() && $this->getPort() !== 443) || (!$this->isHttps() && $this->getPort() !== 80) ? $this->getPort() : ''
		);
	}

	/**
	 * Perform content-type negotiation.
	 *
	 * @param array $priorities A list of content-types to negotiate with.
	 *
	 * @return AcceptHeader|Accept
	 */
	public function negotiate(array $priorities) {
		$accept = $this->getHeader(Header::ACCEPT);

		return empty($accept)
			? null
			: $this->getNegotiator(Negotiator::class)->getBest($accept->getValue(), $priorities);
	}

	/**
	 * Perform language negotiation.
	 *
	 * @param array $priorities A list of languages to negotiate with.
	 *
	 * @return AcceptHeader|AcceptLanguage
	 */
	public function negotiateLanguage(array $priorities) {
		$acceptLanguage = $this->getHeader(Header::ACCEPT_LANGUAGE);

		return empty($acceptLanguage)
			? null
			: $this->getNegotiator(LanguageNegotiator::class)->getBest($acceptLanguage->getValue(), $priorities);
	}

	/**
	 * Perform encoding negotiation.
	 *
	 * @param array $priorities A list of encodings to negotiate with.
	 *
	 * @return AcceptHeader|AcceptEncoding
	 */
	public function negotiateEncoding(array $priorities) {
		$acceptEncoding = $this->getHeader(Header::ACCEPT_ENCODING);

		return empty($acceptEncoding)
			? null
			: $this->getNegotiator(EncodingNegotiator::class)->getBest($acceptEncoding->getValue(), $priorities);
	}

	/**
	 * Perform charset negotiation.
	 *
	 * @param array $priorities A list of charsets to negotiate with.
	 *
	 * @return AcceptHeader|AcceptCharset
	 */
	public function negotiateCharset(array $priorities) {
		$acceptCharset = $this->getHeader(Header::ACCEPT_CHARSET);

		return empty($acceptCharset)
			? null
			: $this->getNegotiator(CharsetNegotiator::class)->getBest($acceptCharset->getValue(), $priorities);
	}

	/**
	 * Determine whether the specified content-type is accepted by the request source.
	 *
	 * @param string $type The content-type to check.
	 *
	 * @return bool
	 */
	public function accepts($type) {
		return !empty($this->negotiate([$type]));
	}

	/**
	 * Determine whether the specified language is accepted by the request source.
	 *
	 * @param string $lang The language to check.
	 *
	 * @return bool
	 */
	public function acceptsLanguage($lang) {
		return !empty($this->negotiateLanguage([$lang]));
	}

	/**
	 * Determine whether the specified encoding is accepted by the request source.
	 *
	 * @param string $encoding The encoding to check.
	 *
	 * @return bool
	 */
	public function acceptsEncoding($encoding) {
		return !empty($this->negotiateEncoding([$encoding]));
	}

	/**
	 * Determine whether the specified charset is accepted by the request source.
	 *
	 * @param string $charset The language to check.
	 *
	 * @return bool
	 */
	public function acceptsCharset($charset) {
		return !empty($this->negotiateCharset([$charset]));
	}

	/**
	 * Attaches {@link Request::param() URL params} to this request.
	 *
	 * @param string $property The property to create.
	 * @param mixed $value The value to attach.
	 */
	public function attachParam($property, $value) {
		$this->_params[$property] = $value;
	}

	/**
	 * Determine whether named data exists.
	 *
	 * @param string $property The named property to check for.
	 *
	 * @return bool
	 */
	public function hasParam($property) {
		return array_key_exists($property, $this->_params);
	}

	/**
	 * Retrieve a URL parameter.
	 *
	 * @param string $property The property to retrieve. If not provided, the entire set of parameters is returned.
	 * @param mixed $fallback A fallback value to provide if the property did not exist.
	 *
	 * @return mixed
	 */
	public function param($property = null, $fallback = null) {
		if (empty($property)) return $this->_params;
		return array_key_exists($property, $this->_params) ? $this->_params[$property] : $fallback;
	}

	/**
	 * Validates the contents of the URL params attached to this request.
	 *
	 * @param Closure $validation A function accepting a {@link Validator validator} as its only argument.
	 */
	public function validateParams(Closure $validation) {
		$validator = new Validator($this->_params);
		$validation($validator);
		$validator->validate();
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
		return array_key_exists($property, $this->_data) ? $this->_data[$property] : $fallback;
	}

	/**
	 * Validates the contents of the body data attached to this request.
	 *
	 * @param Closure $validation A function accepting a {@link Validator validator} as its only argument.
	 */
	public function validateData(Closure $validation) {
		$validator = new Validator($this->_data);
		$validation($validator);
		$validator->validate();
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
		return array_key_exists($property, $this->_query) ? $this->_query[$property] : $fallback;
	}

	/**
	 * Validates the contents of the querystring attached to this request.
	 *
	 * @param Closure $validation A function accepting a {@link Validator validator} as its only argument.
	 */
	public function validateQuery(Closure $validation) {
		$validator = new Validator($this->_query);
		$validation($validator);
		$validator->validate();
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
		return array_key_exists($cookie, $this->_cookies) ? $this->_cookies[$cookie] : $fallback;
	}

	/**
	 * Obtain a possible CSRF token attached to this request.
	 *
	 * @return string
	 */
	public function getCsrfToken() {
		if (!empty($this->getHeader(Header::X_CSRF_TOKEN))) {
			return $this->getHeader(Header::X_CSRF_TOKEN);
		}

		return Utility::evaluate($this->data(), SessionService::CSRF_TOKEN_NAME);
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
		return Utility::evaluate($this->_extra, $prop, $fallback);
	}

	/**
	 * Get a negotiator instance.
	 *
	 * @param string $class The negotiator class name.
	 *
	 * @return Negotiator
	 */
	protected function getNegotiator($class) {
		if (!array_key_exists($class, $this->_negotiators)) {
			$this->_negotiators[$class] = new $class();
		}

		return $this->_negotiators[$class];
	}

	/**
	 * Gets a user attached to this request via the {@link Header::X_USER_TOKEN X-User-Token} header.
	 *
	 * @return User
	 */
	public function getUser() {
		if (empty($this->_user)) {
			if ($this->hasHeader(Header::X_USER_TOKEN)) {
				$this->_user = User::findByToken($this->getHeader(Header::X_USER_TOKEN)->getValue());
			}
		}

		return $this->_user;
	}

}