<?php namespace Tempest\Http\Middleware;

use Closure;
use Tempest\Http\Handler;
use Tempest\Http\Header;

/**
 * Basic protective middleware.
 *
 * @author Marty Wallace
 */
class Protection extends Handler {

	/**
	 * Adds some basic response headers that slightly improve application security.
	 *
	 * @param Closure $next
	 */
	public function protect(Closure $next) {
		$this->expect([
			'nosniff' => true,
			'denyFrames' => true
		]);

		if ($this->option('nosniff')) $this->response->header(Header::X_CONTENT_TYPE_OPTIONS, 'nosniff');
		if ($this->option('denyFrames')) $this->response->header(Header::X_FRAME_OPTIONS, 'deny');

		$next();
	}

}