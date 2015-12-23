<?php namespace Tempest\Http;

use Exception;
use Tempest\Utils\Memoizer;
use Tempest\Models\UploadedFileModel;


/**
 * A request made to the application.
 *
 * @property-read string $method The request method e.g. GET, POST.
 * @property-read string $uri The request URI e.g. /about.
 * @property-read string[] $headers The request headers. Returns an empty array if the getallheaders() function does not
 * exist.
 * @property-read string $ip The IP address making the request.
 *
 * @package Tempest\Http
 * @author Marty Wallace
 */
class Request extends Memoizer {

	/** @var array */
	private $_named = array();

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
		if ($prop === 'method') return strtoupper($_SERVER['REQUEST_METHOD']);
		if ($prop === 'uri') return parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
		if ($prop === 'ip') return $_SERVER['REMOTE_ADDR'];

		if ($prop === 'headers') {
			return $this->memoize('headers', function() {
				$headers = array();

				if (function_exists('getallheaders')) {
					foreach (getallheaders() as $header => $value) {
						$headers[strtolower($header)] = $value;
					}
				}

				return $headers;
			});
		}

		return null;
	}

	public function __isset($prop) {
		return property_exists($this, $prop) ||
			$this->{$prop} !== null;
	}

	/**
	 * Returns request data e.g. GET or POST data, based on the request method.
	 *
	 * @param string $name The name of the data to get.
	 * @param mixed $fallback A fallback value to use if the data does not exist.
	 *
	 * @return mixed
	 */
	public function data($name = null, $fallback = null) {
		$stack = $this->memoize('_stack', function() {
			if ($this->method === 'GET') return $_GET;
			if ($this->method === 'POST') return $_POST;

			return array();
		});

		if ($name === null) return $stack;

		return array_key_exists($name, $stack) ? $stack[$name] : $fallback;
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
	 * Returns an UploadedFileModel representing a file that was sent in the request.
	 *
	 * @param string $name The name associated with the uploaded file.
	 *
	 * @return UploadedFileModel|null An UploadedFileModel if a file was uploaded, else null.
	 *
	 * @throws Exception If there was an issue uploading the file because it was too large, the tmp folder is not
	 * writable or some other error outlined in {@link http://php.net/manual/en/features.file-upload.errors.php}.
	 */
	public function file($name) {
		return $this->memoize('_file_' . $name, function() use ($name) {
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

		return array_key_exists($name, $this->headers) ? $this->headers[$name] : $fallback;
	}

}