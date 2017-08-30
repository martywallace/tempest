<?php namespace Tempest\Http\Middleware;

use Exception;
use Closure;
use Tempest\Http\{
	Handler, Header, ContentType, Status
};

/**
 * Inbuilt body parser for populating data contained in request bodies.
 *
 * @author Marty Wallace
 */
class BodyParsing extends Handler {

	const OPTION_TRIM = 'trim';

	/**
	 * Parse the request and attach data based on its body.
	 *
	 * @see BodyParsing::OPTION_TRIM
	 *
	 * @param Closure $next
	 *
	 * @throws Exception
	 */
	public function parse(Closure $next) {
		$this->expect([
			self::OPTION_TRIM => true
		]);

		if (!empty($this->request->getBody())) {
			$data = [];

			if ($this->request->getHeader(Header::CONTENT_TYPE) === ContentType::APPLICATION_JSON) {
				$data = json_decode($this->request->getBody());

				if (json_last_error() !== JSON_ERROR_NONE) {
					$this->response->setStatus(Status::BAD_REQUEST)->text('The request contained invalid JSON: ' . json_last_error_msg());
					return;
				}
			}

			if ($this->request->getHeader(Header::CONTENT_TYPE) === ContentType::APPLICATION_X_WWW_FORM_URLENCODED) {
				parse_str($this->request->getBody(), $data);
			}

			foreach ($data as $property => $value) {
				if ($this->option(self::OPTION_TRIM) && is_string($value)) $value = trim($value);

				$this->request->attachData($property, $value);
			}
		}

		$next();
	}

}