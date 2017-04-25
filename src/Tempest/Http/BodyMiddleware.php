<?php namespace Tempest\Http;

use Exception;
use Tempest\Tempest;
use Tempest\Utils\JSONUtil;

/**
 * Middleware used to parse the request body into usable data.
 *
 * @package Tempest\Http
 * @author Marty Wallace
 */
class BodyMiddleware extends Middleware {

	/**
	 * Parse and attach the request body to the request object for usage in future middleware or controller actions.
	 *
	 * @param Request $request
	 * @param Response $response
	 * @param callable $next
	 *
	 * @return mixed
	 */
	public function parse(Request $request, Response $response, callable $next) {
		$stack = Tempest::get()->memoization->cache(static::class, 'parseComplex', function() use ($request, $response) {
			if ($request->method === 'GET') {
				return $_GET;
			} else {
				$data = [];

				if (ContentType::matches($request->contentType, ContentType::APPLICATION_X_WWW_FORM_URLENCODED)) {
					parse_str($request->body, $data);
				} else if (ContentType::matches($request->contentType, ContentType::APPLICATION_JSON)) {
					$data = JSONUtil::decode($request->body, true);
				} else {
					if ($request->method === 'POST') return $_POST;
					else throw new Exception('Cannot extract data from a "' . $request->method . '" request with the content-type "' . $request->contentType . '".');
				}

				return $data;
			}
		});

		foreach ($stack as $name => $value) $request->setData($name, $value);

		return $next();
	}

	public function parseFiles(Request $request, Response $response, callable $next) {
		if ($request->hasFiles()) {
			// TODO.
		}

		return $next();
	}

}