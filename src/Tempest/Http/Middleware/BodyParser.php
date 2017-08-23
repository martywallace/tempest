<?php namespace Tempest\Http\Middleware;

use Exception;
use Closure;
use Tempest\Http\{Handler, Header, ContentType};

/**
 * Inbuilt body parser for populating data contained in request bodies.
 *
 * @author Marty Wallace
 */
class BodyParser extends Handler {

	/**
	 * Parse the request and attach data based on its body.
	 *
	 * @param Closure $next
	 *
	 * @throws Exception
	 */
	public function parse(Closure $next) {
		$data = [];

		if ($this->request->header(Header::CONTENT_TYPE) === ContentType::APPLICATION_JSON) {
			$data = json_decode($this->request->body);

			if (json_last_error() !== JSON_ERROR_NONE) {
				throw new Exception('The request contained invalid JSON: ' . json_last_error_msg());
			}
		}

		if ($this->request->header(Header::CONTENT_TYPE) === ContentType::APPLICATION_X_WWW_FORM_URLENCODED) {
			parse_str($this->request->body, $data);
		}

		foreach ($data as $property => $value) {
			$this->request->attachData($property, $value);
		}

		$next();
	}

	public function sanitize(Closure $next) {
		$next();
	}

}