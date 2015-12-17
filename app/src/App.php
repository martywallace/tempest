<?php

use Tempest\Tempest;
use Services\GeneralService;

/**
 * Your application. The primary purpose of this class is to bind your services to the application.
 *
 * @property-read GeneralService $general The example service included in the Tempest template.
 */
class App extends Tempest {

	/**
	 * Bind custom services to your application.
	 *
	 * @return \Tempest\Services\Service[]
	 */
	protected function bindServices() {
		return array(
			'general' => new GeneralService()
		);
	}

	/**
	 * Additional application setup to perform (e.g. adding Twig extensions). This occurs after services are bound and
	 * before any routing is attempted.
	 */
	protected function setup() {
		// ...
	}

}