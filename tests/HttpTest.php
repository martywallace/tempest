<?php namespace Tempest\Tests;

use Closure;
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
	public function testCreateRoutes(App $app) {
		$provider = function(Http $http) {
			return [
				$http->get('/')->controller(ExampleController::do()),
				$http->get('/template')->template('example.html')
			];
		};

		$http = new Http($provider);

		$this->assertCount(2, $http->getRoutes());
		$this->assertEquals('/', $http->getRoutes()[0]->getUri());
		$this->assertEquals('GET', $http->getRoutes()[1]->getMethod()[0]);

		return $provider;
	}

	/**
	 * @depends testApp
	 * @depends testCreateRoutes
	 */
	public function testResponse(App $app, Closure $routes) {
		$request = new Request('GET', '/');

		$response = $app->http($request, $routes);

		$this->assertInstanceOf(Response::class, $response);

		$this->assertEquals('Test', $response->getBody());
		$this->assertEquals(Status::OK, $response->getStatus());

		return $response;
	}

}