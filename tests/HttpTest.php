<?php namespace Tempest\Tests;

use Closure;
use Tempest\Http\ContentType;
use Tempest\Http\Header;
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

	public function testCreateRoutes() {
		$provider = function(Http $http) {
			return [
				$http->get('/')->controller(ExampleController::do()),
				$http->get('/json')->controller(ExampleController::do('json')),
				$http->get('/template')->template('example.html')
			];
		};

		$http = new Http($provider);

		$this->assertCount(3, $http->getRoutes());
		$this->assertEquals('/', $http->getRoutes()[0]->getUri());
		$this->assertEquals('GET', $http->getRoutes()[1]->getMethod()[0]);

		return $provider;
	}

	/**
	 * @depends testApp
	 * @depends testCreateRoutes
	 */
	public function testTextResponse(App $app, Closure $routes) {
		$request = new Request('GET', '/');
		$response = $app->http($request, $routes);

		$this->assertInstanceOf(Response::class, $response);
		$this->assertEquals('Test', $response->getBody());
		$this->assertEquals(Status::OK, $response->getStatus());
		$this->assertEquals(ContentType::TEXT_PLAIN, $response->getType());
	}

	/**
	 * @depends testApp
	 * @depends testCreateRoutes
	 */
	public function testJsonResponse(App $app, Closure $routes) {
		$request = new Request('GET', '/json');
		$response = $app->http($request, $routes);

		$this->assertEquals(ContentType::APPLICATION_JSON, $response->getType());
		$this->assertEquals('{"test":10}', $response->getBody());
	}

}