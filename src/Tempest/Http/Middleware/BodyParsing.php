<?php namespace Tempest\Http\Middleware;

use Exception;
use Closure;
use Tempest\Http\Middleware;
use Tempest\Http\Request;
use Tempest\Http\Response;
use Tempest\Http\Header;
use Tempest\Http\ContentType;
use Tempest\Http\Status;

/**
 * Inbuilt body parser for populating data contained in request bodies.
 *
 * @author Marty Wallace
 */
class BodyParsing extends Middleware {

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

			if (stripos($request->getHeader(Header::CONTENT_TYPE)->getValue(), ContentType::APPLICATION_JSON) !== false) {
				$data = json_decode($request->getBody());

				if (json_last_error() !== JSON_ERROR_NONE) {
					$response->setStatus(Status::BAD_REQUEST)->text('The request contained invalid JSON: ' . json_last_error_msg());
					return;
				}
			}
			if (stripos($request->getHeader(Header::CONTENT_TYPE)->getValue(), ContentType::APPLICATION_X_WWW_FORM_URLENCODED) !== false) {
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