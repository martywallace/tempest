<?php namespace Tempest\Http\Middleware;

use Closure;
use Exception;
use Tempest\App;
use Tempest\Http\Handler;
use Tempest\Extensions\FileSessionHandler;

/**
 * Middleware dealing with data persistence (e.g. sessions).
 *
 * @author Marty Wallace
 */
class Sessions extends Handler {

	const NAME = 'name';
	const PATH = 'path';

	/**
	 * Start filesystem-based session storage.
	 *
	 * @see Sessions::NAME
	 * @see Sessions::PATH
	 * @see Sessions::CREATE_CSRF_TOKEN
	 *
	 * @param Closure $next
	 *
	 * @throws Exception
	 */
	public function filesystem(Closure $next) {
		$this->expect([
			self::NAME => 'SessionID',
			self::PATH => session_save_path()
		]);

		App::get()->session->start(new FileSessionHandler(
			$this->option(self::PATH),
			$this->option(self::NAME)
		));

		$next();
	}

}