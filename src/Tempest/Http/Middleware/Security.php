<?php namespace Tempest\Http\Middleware;

use Closure;
use Exception;
use Tempest\App;
use Tempest\Http\{
	Handler, Header, Status
};

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
	public function csrf(Closure $next) {
		if ($this->request->getMethod() === 'GET') {
			$next();
		} else {
			if (!App::get()->session->active()) throw new Exception('There is no active session to read a CSRF token from.');
			if (!App::get()->session->has('csrfToken')) throw new Exception('There is no CSRF token available.');
			if (empty($this->request->csrfToken())) throw new Exception('Missing CSRF token.');

			if (hash_equals(App::get()->session->get('csrfToken'), $this->request->csrfToken())) {
				$next();
			} else {
				$this->response->setStatus(Status::BAD_REQUEST)->text('Invalid CSRF token.');
			}
		}
	}

}