<?php namespace Tempest\Http\Middleware;

use Closure;
use Exception;
use Tempest\App;
use Tempest\Http\Middleware;
use Tempest\Http\Request;
use Tempest\Http\Response;
use Tempest\Http\Header;
use Tempest\Http\Status;

/**
 * Basic protective middleware.
 *
 * @author Marty Wallace
 */
class Security extends Middleware {

	const OPTION_NOSNIFF = 'nosniff';
	const OPTION_DENY_FRAMES = 'denyFrames';
	const OPTION_XSS_PROTECTION = 'xssProtection';

	/**
	 * Adds some basic response headers that slightly improve application security.
	 *
	 * @see Security::OPTION_NOSNIFF
	 * @see Security::OPTION_DENY_FRAMES
	 * @see Security::OPTION_XSS_PROTECTION
	 *
	 * @param Request $request
	 * @param Response $response
	 * @param Closure $next
	 */
	public function headers(Request $request, Response $response, Closure $next) {
		$this->expect([
			self::OPTION_NOSNIFF => true,
			self::OPTION_DENY_FRAMES => true,
			self::OPTION_XSS_PROTECTION => true
		]);

		if ($this->option(self::OPTION_NOSNIFF)) $response->setHeader(Header::X_CONTENT_TYPE_OPTIONS, 'nosniff');
		if ($this->option(self::OPTION_DENY_FRAMES)) $response->setHeader(Header::X_FRAME_OPTIONS, 'sameorigin');
		if ($this->option(self::OPTION_XSS_PROTECTION)) $response->setHeader(Header::X_XSS_PROTECTION, '1; mode=block');

		$next();
	}

	/**
	 * Validates the request against the current CSRF token.
	 *
	 * @param Request $request
	 * @param Response $response
	 * @param Closure $next
	 *
	 * @throws Exception
	 */
	public function validateCsrf(Request $request, Response $response, Closure $next) {
		if ($request->getMethod() === 'GET') {
			$next();
		} else {
			if (!App::get()->session->active()) throw new Exception('There is no active session to read a CSRF token from.');

			if (empty($request->getCsrfToken())) {
				$response->setStatus(Status::UNAUTHORIZED)->text('Missing CSRF token.');
			} else {
				if (hash_equals(App::get()->session->getCsrfToken(), $request->getCsrfToken())) {
					$next();
				} else {
					$response->setStatus(Status::UNAUTHORIZED)->text('Invalid CSRF token.');
				}
			}
		}
	}

	/**
	 * Regenerates the CSRF token.
	 *
	 * @param Request $request
	 * @param Response $response
	 * @param Closure $next
	 */
	public function regenerateCsrf(Request $request, Response $response, Closure $next) {
		App::get()->session->regenerateCsrfToken();
		$next();
	}

}