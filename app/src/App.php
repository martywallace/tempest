<?php

use Tempest\Tempest;
use Services\GeneralService;

/**
 * Your application. The primary purpose of this class is to bind your services to the application.
 *
 * @property-read GeneralService $general
 */
class App extends Tempest {

	protected function bindServices() {
		return array(
			'general' => new GeneralService()
		);
	}

}