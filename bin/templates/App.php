<?php

use Tempest\App as BaseApp;

/**
 * Your core application class.
 */
class App extends BaseApp {

	protected function setup() {
		date_default_timezone_set('Australia/Sydney');
	}

	protected function services() {
		return [
			// Add your application services here.
			// ...
		];
	}

}