<?php

use Tempest\Tempest;


class App extends Tempest {

    protected function bindComponents() {
		return array(
			// Bind Components to the application here.
			// ...
		);
	}

	protected function bindControllers() {
		return array(
			new \Controllers\GeneralController()
		);
	}

}