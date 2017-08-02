<?php namespace Tempest\Tests\Material;

use Tempest\App as BaseApp;

/**
 * @property-read ExampleService $example
 */
class App extends BaseApp {

	protected function services() {
		return [
			'example' => ExampleService::class
		];
	}

}