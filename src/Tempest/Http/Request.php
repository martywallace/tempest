<?php namespace Tempest\Http;

use Exception;
use Tempest\Tempest;
use Tempest\Utils\JSONUtil;
use Tempest\Utils\ObjectUtil;
use Tempest\Models\UploadedFileModel;


/**
 * A request made to the application.
 *
 * @property-read string $method The request method e.g. GET, POST.
 * @property-read string $contentType The request content-type.
 * @property-read string $uri The request URI e.g. /about.
 * @property-read string[] $headers The request headers. Returns an empty array if the {@link getallheaders()} function
 * does not exist.
 * @property-read string $ip The IP address making the request.
 * @property-read string $body The raw request body.
 *
 * @package Tempest\Http
 * @author Marty Wallace
 */
final class Request {

	/** @var array */
	private $_data = [];

	/** @var array */
	private $_named = [];

	public function __get($prop) {
		if ($prop === 'method') {
			if (array_key_exists('x-http-method-override', $this->headers)) {
				return strtoupper($this->headers['x-http-method-override']);
			}

			return strtoupper($_SERVER['REQUEST_METHOD']);
		}

		if ($prop === 'contentType') return strtolower($_SERVER['CONTENT_TYPE']);
		if ($prop === 'ip') return $_SERVER['REMOTE_ADDR'];
		
		if ($prop === 'uri') {
			return Tempest::get()->memoization->cache(static::class, 'uri', function() {
				$base = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

				if (empty($base) || $base === '/') {
					return '/';
				}

				return rtrim($base, '/');
			});
		}

		if ($prop === 'headers') {
			return Tempest::get()->memoization->cache(static::class, 'headers', function() {
				$headers = [];

				if (function_exists('getallheaders')) {
					foreach (getallheaders() as $header => $value) {
						$headers[strtolower($header)] = $value;
					}
				}

				return $headers;
			});
		}

		if ($prop === 'body') {
			return Tempest::get()->memoization->cache(static::class, 'body', function() {
				return file_get_contents('php://input');
			});
		}

		return null;
	}

	public function __isset($prop) {
		return property_exists($this, $prop) ||
			$this->{$prop} !== null;
	}

	/**
	 * Attaches data provided by named route components.
	 *
	 * @param string $name The name of the named data for reference via {@link named()}.
	 * @param mixed $value THe value to attach.
	 */
	public function setNamed($name, $value) {
		$this->_named[$name] = $value;
	}

	/**
	 * Attaches data.
	 *
	 * @param string $name The name of the named data for reference via {@link data()}.
	 * @param mixed $value THe value to attach.
	 */
	public function setData($name, $value) {
		$this->_data[$name] = $value;
	}

	/**
	 * Get request data. This method only behaves correctly if the request content-type is
	 * {@link ContentType::APPLICATION_X_WWW_FORM_URLENCODED} or {@link ContentType::APPLICATION_JSON}, or the request
	 * method is POST.
	 *
	 * @param string $name The name of the data to get.
	 * @param mixed $fallback A fallback value to use if the data does not exist.
	 *
	 * @return mixed
	 */
	public function data($name = null, $fallback = null) {
		return $name === null ? $this->_data
			: (array_key_exists($name, $this->_data) ? $this->_data[$name] : $fallback);
	}

	/**
	 * Return data provided in the request URI against dynamic named components.
	 *
	 * @param string $name The argument name provided in the route definition.
	 * @param mixed $fallback A fallback value to use if the argument was not provided.
	 *
	 * @return mixed
	 */
	public function named($name = null, $fallback = null) {
		return $name === null ? $this->_named
			: (array_key_exists($name, $this->_named) ? $this->_named[$name] : $fallback);
	}

	/**
	 * Returns a header from the request.
	 *
	 * @param string $name The header name.
	 * @param mixed $fallback A fallback value to use if the header was not provided.
	 *
	 * @return mixed
	 */
	public function header($name, $fallback = null) {
		$name = strtolower($name);
		return ObjectUtil::getDeepValue($this->headers, $name, $fallback);
	}

	/**
	 * Determine whether the request has files attached to it.
	 *
	 * @return bool
	 */
	public function hasFiles() {
		return !empty($_FILES);
	}

}