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

	public function testBootApp() {
		$app = App::boot(__DIR__);

		$this->assertInstanceOf(App::class, $app);
		$this->assertInstanceOf(App::class, App::get());

		return App::get();
	}

	/**
	 * @depends testBootApp
	 */
	public function testGeneratesValidRoot(App $app) {
		$this->assertEquals(realpath(__DIR__ . '/../'), $app->root);
	}

}