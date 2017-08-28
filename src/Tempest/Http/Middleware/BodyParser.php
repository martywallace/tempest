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

	const TRIM = 'trim';

	/**
	 * Parse the request and attach data based on its body.
	 *
	 * @see BodyParser::TRIM
	 *
	 * @param Closure $next
	 *
	 * @throws Exception
	 */
	public function parse(Closure $next) {
		$this->expect([
			self::TRIM => true
		]);

		if (!empty($this->request->getBody())) {
			$data = [];

			if ($this->request->getHeader(Header::CONTENT_TYPE) === ContentType::APPLICATION_JSON) {
				$data = json_decode($this->request->getBody());

				if (json_last_error() !== JSON_ERROR_NONE) {
					throw new Exception('The request contained invalid JSON: ' . json_last_error_msg());
				}
			}

			if ($this->request->getHeader(Header::CONTENT_TYPE) === ContentType::APPLICATION_X_WWW_FORM_URLENCODED) {
				parse_str($this->request->getBody(), $data);
			}

			foreach ($data as $property => $value) {
				if ($this->option(self::TRIM) && is_string($value)) $value = trim($value);

				$this->request->attachData($property, $value);
			}
		}

		$next();
	}

}