<?php namespace Tempest\Http\Middleware;

use Exception;
use Closure;
use Tempest\Http\{Handler, Request, Response, Header, ContentType, Status};

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
	 * @param Request $request
	 * @param Response $response
	 * @param Closure $next
	 *
	 * @throws Exception
	 */
	public function parse(Request $request, Response $response, Closure $next) {
		$this->expect([
			self::OPTION_TRIM => true
		]);

		if (!empty($request->getBody())) {
			$data = [];

			if (stripos($request->getHeader(Header::CONTENT_TYPE), ContentType::APPLICATION_JSON) >= 0) {
				$data = json_decode($request->getBody());

				if (json_last_error() !== JSON_ERROR_NONE) {
					$response->setStatus(Status::BAD_REQUEST)->text('The request contained invalid JSON: ' . json_last_error_msg());
					return;
				}
			}

			if (stripos($request->getHeader(Header::CONTENT_TYPE), ContentType::APPLICATION_X_WWW_FORM_URLENCODED) >= 0) {
				parse_str($request->getBody(), $data);
			}

			foreach ($data as $property => $value) {
				if ($this->option(self::OPTION_TRIM) && is_string($value)) $value = trim($value);

				$request->attachData($property, $value);
			}
		}

		$next();
	}

}