<?php namespace Tempest\Services;

use Tempest\App;
use Monolog\Logger;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Handler\SlackHandler;

/**
 * Handles application logging.
 *
 * @author Marty Wallace
 */
class LogService extends Logger implements Service {

	public function __construct() {
		parent::__construct('app');

		if (!empty(App::get()->storage)) {
			$handler = new RotatingFileHandler(App::get()->storage . '/logs/app.log');
			$this->pushHandler($handler);
		}

		if (!empty(App::get()->config('slack'))) {
			$handler = new SlackHandler(
				App::get()->config('slack.token'),
				App::get()->config('slack.channel'),
				App::get()->config('slack.name', 'Log')
			);

			$this->pushHandler($handler);
		}
	}

}