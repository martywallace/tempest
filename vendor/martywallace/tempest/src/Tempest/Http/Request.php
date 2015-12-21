<?php namespace Tempest\Http;

use Exception;
use Tempest\Utils\Memoizer;
use Tempest\Models\UploadedFileModel;


/**
 * A request made to the application.
 *
 * @property-read string $method The request method e.g. GET, POST.
 * @property-read string[] $headers The request headers. Returns an empty array if the getallheaders() function does not exist.
 * @property-read string $ip The IP address making the request.
 *
 * @package Tempest\Http
 * @author Marty Wallace
 */
class Request extends Memoizer {

	/** @var array */
	private $_args;

	public function __construct(Array $args) {
		$this->_args = $args;
	}

	public function __get($prop) {
		if ($prop === 'method') return app()->router->method;
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
		$stack = array();

		if (app()->router->method === 'GET') $stack = $_GET;
		if (app()->router->method === 'POST') $stack = $_POST;

		if ($name === null) {
			return $stack;
		}

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
		return $name === null ? $this->_args
			: (array_key_exists($name, $this->_args) ? $this->_args[$name] : $fallback);
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