<?php namespace Tempest\Tests;

use Tempest\Http\ContentType;
use Tempest\Http\Http;
use Tempest\Http\Request;
use Tempest\Http\Response;
use Tempest\Http\Status;
use Tempest\Tests\Material\App;
use PHPUnit\Framework\TestCase;
use Tempest\Tests\Material\ExampleController;

class HttpTest extends TestCase {

	/**
	 * @runInSeparateProcess
	 */
	public function testApp() {
		$app = App::boot(__DIR__, [
			'dev' => true,
			'templates' => 'templates'
		]);

		$this->assertInstanceOf(App::class, $app);

		return $app;
	}

	/**
	 * @depends testApp
	 */
	public function testResponse(App $app) {
		$request = new Request('GET', '/');

		$response = $app->http($request, function(Http $http) {
			return [
				$http->get('/')->controller(ExampleController::do())
			];
		});

		$this->assertInstanceOf(Response::class, $response);

		$this->assertEquals('Test', $response->getBody());
		$this->assertEquals(Status::OK, $response->getStatus());

		return $response;
	}

}