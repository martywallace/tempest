<?php

use Tempest\Tempest;

class App extends Tempest {

    protected function bindServices() {
		return [
			'general' => new \Services\GeneralService()
		];
	}

	protected function bindControllers() {
		return [
			new \Controllers\GeneralController()
		];
	}

}