<?php

require('./vendor/autoload.php');

use Tempest\Tempest;
use PHPUnit\Framework\TestCase;

class App extends Tempest {
	protected function services() { return []; }
	protected function setup() { }
}

class GeneralTest extends TestCase {

	public function instantiate() {
		return App::instantiate(__DIR__, [
			'example' => 10,
			'nested' => [
				'value' => 'hello'
			]
		]);
	}

	/**
	 * @depends instantiate
	 */
	public function testConfig(App $app) {
		$this->assertArrayHasKey('example', $app->config());
		$this->assertEquals('hello', $app->config('nested.value'));
	}

}