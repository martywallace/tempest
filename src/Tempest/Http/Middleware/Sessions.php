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

	/**
	 * Start filesystem-based session storage.
	 *
	 * @param Closure $next
	 *
	 * @throws Exception
	 */
	public function filesystem(Closure $next) {
		$this->expect([
			'name' => 'SessionID',
			'path' => session_save_path()
		]);

		App::get()->session->start(new FileSessionHandler(
			$this->option('path'),
			$this->option('name')
		));

		$next();
	}

}