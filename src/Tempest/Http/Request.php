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
	private $_named = [];

	/**
	 * Attaches data provided by named route components. This method is used internally by the router when a route
	 * providing named data was matched. It should not be called directly.
	 *
	 * @param array $named The named data as key => value pairs.
	 *
	 * @throws Exception If more than one attempt is made to attach named data.
	 */
	public function attachNamed(array $named) {
		if (empty($this->named)) {
			$this->_named = $named;
		} else {
			throw new Exception('Named data has already been attached to this Request.');
		}
	}

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
		$stack = Tempest::get()->memoization->cache(static::class, '_stack', function() {
			if ($this->method === 'GET') {
				return $_GET;
			} else {
				$data = [];

				if (ContentType::matches($this->contentType, ContentType::APPLICATION_X_WWW_FORM_URLENCODED)) {
					parse_str($this->body, $data);
				} else if (ContentType::matches($this->contentType, ContentType::APPLICATION_JSON)) {
					$data = JSONUtil::decode($this->body, true);
				} else {
					// PHP's $_POST knows what's up with various content-types so we'll have one more crack.
					if ($this->method === 'POST') {
						return $_POST;
					} else {
						throw new Exception('Cannot extract data from a "' . $this->method . '" request with the content-type "' . $this->contentType . '".');
					}
				}

				return $data;
			}
		});

		if ($name === null) return $stack;

		return ObjectUtil::getDeepValue($stack, $name, $fallback);
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
	 * Returns an {@link UploadedFileModel} representing a file that was sent in the request.
	 *
	 * @param string $name The name associated with the uploaded file.
	 *
	 * @return UploadedFileModel|null An UploadedFileModel if a file was uploaded, else null.
	 *
	 * @throws Exception If there was an issue uploading the file because it was too large, the tmp folder is not
	 * writable or some other error outlined in {@link http://php.net/manual/en/features.file-upload.errors.php}.
	 */
	public function file($name) {
		return Tempest::get()->memoization->cache(static::class, '_file_' . $name, function() use ($name) {
			$file = isset($_FILES[$name]) ? $_FILES[$name] : null;

			if (!empty($file)) {
				if ($file['error'] === UPLOAD_ERR_OK) {
					// Successful upload.
					$file = new UploadedFileModel($file);
				} else if($file['error'] === UPLOAD_ERR_NO_FILE) {
					// Just return null for a value that wasn't provided (keep it consistent with data() and named()).
					return null;
				} else {
					// Throw exceptions for the other stuff.
					switch ($file['error']) {
						case UPLOAD_ERR_INI_SIZE:
						case UPLOAD_ERR_FORM_SIZE:
							throw new Exception('The filesize of the uploaded file was too large.');
							break;

						case UPLOAD_ERR_PARTIAL:
							throw new Exception('The file was only partially uploaded.');
							break;

						case UPLOAD_ERR_NO_TMP_DIR:
						case UPLOAD_ERR_CANT_WRITE:
							throw new Exception('The file could not be uploaded.');
							break;
					}
				}
			}

			return $file;
		});
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

}