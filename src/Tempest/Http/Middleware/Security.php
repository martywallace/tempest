<?php namespace Tempest\Http\Middleware;

use Closure;
use Exception;
use Tempest\App;
use Tempest\Http\{Handler, Header, Status};

/**
 * Basic protective middleware.
 *
 * @author Marty Wallace
 */
class Security extends Handler {

	const NOSNIFF = 'nosniff';
	const DENY_FRAMES = 'denyFrames';
	const XSS_PROTECTION = 'xssProtection';

	/**
	 * Adds some basic response headers that slightly improve application security.
	 *
	 * @see Security::NOSNIFF
	 * @see Security::DENY_FRAMES
	 * @see Security::XSS_PROTECTION
	 *
	 * @param Closure $next
	 */
	public function headers(Closure $next) {
		$this->expect([
			self::NOSNIFF => true,
			self::DENY_FRAMES => true,
			self::XSS_PROTECTION => true
		]);

		if ($this->option(self::NOSNIFF)) $this->response->setHeader(Header::X_CONTENT_TYPE_OPTIONS, 'nosniff');
		if ($this->option(self::DENY_FRAMES)) $this->response->setHeader(Header::X_FRAME_OPTIONS, 'sameorigin');
		if ($this->option(self::XSS_PROTECTION)) $this->response->setHeader(Header::X_XSS_PROTECTION, '1; mode=block');

		$next();
	}

	/**
	 * Validates the request against the current CSRF token.
	 *
	 * @param Closure $next
	 *
	 * @throws Exception
	 */
	public function validateCsrf(Closure $next) {
		if ($this->request->getMethod() === 'GET') {
			$next();
		} else {
			if (!App::get()->session->active()) throw new Exception('There is no active session to read a CSRF token from.');

			if (empty($this->request->getCsrfToken())) {
				$this->response->setStatus(Status::UNAUTHORIZED)->text('Missing CSRF token.');
			} else {
				if (hash_equals(App::get()->session->getCsrfToken(), $this->request->getCsrfToken())) {
					$next();
				} else {
					$this->response->setStatus(Status::UNAUTHORIZED)->text('Invalid CSRF token.');
				}
			}
		}
	}

	/**
	 * Regenerates the CSRF token.
	 *
	 * @param Closure $next
	 */
	public function regenerateCsrf(Closure $next) {
		App::get()->session->regenerateCsrfToken();

		$next();
	}

}