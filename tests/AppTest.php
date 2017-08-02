<?php namespace Tempest\Tests;

use Exception;
use PHPUnit\Framework\TestCase;
use Tempest\Tests\Material\App;

class AppTest extends TestCase {

	public function testCannotGetWithoutBoot() {
		$this->expectException(Exception::class);
		$this->expectExceptionMessage('Missing preceding call to App::boot().');

		App::get();
	}

	/**
	 * @runInSeparateProcess
	 */
	public function testCannotBootMoreThanOnce() {
		$this->expectException(Exception::class);

		App::boot(__DIR__);
		App::boot(__DIR__);
	}

	/**
	 * @runInSeparateProcess
	 */
	public function testCannotBootWithDotConfig() {
		$this->expectException(Exception::class);

		App::boot(__DIR__, [
			'some.thing' => 1
		]);
	}

	public function testBootApp() {
		$app = App::boot(__DIR__, [
			'a' => 10,
			'b' => [
				'c' => 20
			]
		]);

		$this->assertInstanceOf(App::class, $app);
		$this->assertInstanceOf(App::class, App::get());

		return App::get();
	}

	/**
	 * @depends testBootApp
	 */
	public function testGeneratesValidRoot(App $app) {
		$this->assertEquals(__DIR__, $app->root);
	}

	/**
	 * @depends testBootApp
	 */
	public function testCanGetAppConfig(App $app) {
		$this->assertEquals(10, $app->config('a'));
		$this->assertEquals(20, $app->config('b.c'));
		$this->assertEquals(null, $app->config('c'));
		$this->assertEquals('someFallbackValue', $app->config('b.d', 'someFallbackValue'));
		$this->assertArrayHasKey('a', $app->config());
	}

	/**
	 * @depends testBootApp
	 */
	public function testHasExampleService(App $app) {
		$this->assertTrue($app->hasService('example'));
	}

	/**
	 * @depends testBootApp
	 */
	public function testExampleServiceNotYetBooted(App $app) {
		$this->assertFalse($app->hasBootedService('example'));
	}

	/**
	 * @runInSeparateProcess
	 */
	public function testCanBootAndCallExampleService() {
		$app = App::boot('');

		$value = $app->example->test();

		$this->assertEquals(5, $value);
		$this->assertEquals(5, $app->getService('example')->test());
		$this->assertEquals(5, App::get()->example->test());
		$this->assertEquals(5, App::get()->getService('example')->test());
		$this->assertTrue($app->hasBootedService('example'));
	}

	/**
	 * @depends testBootApp
	 */
	public function testCannotGetNonexistentService(App $app) {
		$this->expectException(Exception::class);
		$this->expectExceptionMessage('Service "nope" does not exist.');

		$app->getService('nope');
	}

}