<?php

use Tempest\Tempest;
use Services\GeneralService;
use Controllers\GeneralController;

/**
 * Your application.
 *
 * @property-read GeneralService $general
 */
class App extends Tempest {

    protected function bindServices() {
		return array(
			'general' => new GeneralService()
		);
	}

	protected function bindControllers() {
		return array(
			new GeneralController()
		);
	}

}