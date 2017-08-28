<?php namespace Tempest\Tests;

use Closure;
use Tempest\Http\ContentType;
use Tempest\Http\Header;
use Tempest\Http\Http;
use Tempest\Http\Middleware\BodyParser;
use Tempest\Http\Request;
use Tempest\Http\Response;
use Tempest\Http\Status;
use Tempest\Http\Route;
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

	public function testCreateRouteGroups() {
		$provider = function(Http $http) {
			return [
				$http->get('/')->template('example.html'),
				$http->group('/api', [
					$http->get('/')->controller(ExampleController::do('getAll')),
					$http->group('/dogs', [
						$http->get('/')->controller(ExampleController::do('getDogs')),
						$http->post('/')->controller(ExampleController::do('createDog'))
					]),
					$http->group('/cats', [
						$http->get('/')->controller(ExampleController::do('getCats')),
						$http->post('/')->controller(ExampleController::do('createCat'))
					])
				])
			];
		};

		$http = new Http($provider);

		$uris = array_values(array_map(function(Route $route) {
			return [$route->getUri(), $route->getMethod()];
		}, $http->getRoutes()));

		$this->assertCount(6, $http->getRoutes());

		$this->assertEquals([
			['/', ['GET']],
			['/api', ['GET']],
			['/api/dogs', ['GET']],
			['/api/dogs', ['POST']],
			['/api/cats', ['GET']],
			['/api/cats', ['POST']]
		], $uris);
	}

	/**
	 * @depends testApp
	 */
	public function testParseJsonBody(App $app) {
		$provider = function(Http $http) {
			$http->middleware(BodyParser::do('parse', [BodyParser::TRIM => true]));

			return [
				$http->post('/')->controller(ExampleController::do('convertJson'))
			];
		};

		$request = Request::make('POST', '/', [
			Header::CONTENT_TYPE => ContentType::APPLICATION_JSON
		], '{"first":1,"second":"  two   "}');

		$response = $app->http($request, $provider);

		$this->assertEquals(1, $request->data('first'));
		$this->assertEquals('two', $request->data('second'));
		$this->assertEquals('{"first":1,"second":"two"}', $response->getBody());
	}

	/**
	 * @depends testApp
	 * @depends testCreateRoutes
	 */
	public function testTextResponse(App $app, Closure $routes) {
		$response = $app->http(Request::make('GET', '/'), $routes);

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
		$response = $app->http(Request::make('GET', '/json'), $routes);

		$this->assertEquals(ContentType::APPLICATION_JSON, $response->getType());
		$this->assertEquals('{"test":10}', $response->getBody());
	}

	/**
	 * @depends testApp
	 * @depends testCreateRoutes
	 */
	public function testTemplateResponse(App $app, Closure $routes) {
		$response = $app->http(Request::make('GET', '/template'), $routes);

		$this->assertEquals($app->twig->render('example.html'), $response->getBody());
		$this->assertEquals(ContentType::TEXT_HTML, $response->getType());
	}

	/**
	 * @depends testApp
	 * @depends testCreateRoutes
	 */
	public function testNotFound(App $app, Closure $routes) {
		$response = $app->http(Request::make('GET', '/doesnotexist'), $routes);

		$this->assertEquals(Status::NOT_FOUND, $response->getStatus());
		$this->assertEquals($app->twig->render('404.html'), $response->getBody());
	}

	/**
	 * @depends testApp
	 * @depends testCreateRoutes
	 */
	public function testMethodNotAllowed(App $app, Closure $routes) {
		$response = $app->http(Request::make('POST', '/'), $routes);

		$this->assertEquals(Status::METHOD_NOT_ALLOWED, $response->getStatus());
		$this->assertEquals($app->twig->render('405.html'), $response->getBody());
		$this->assertEquals('GET', $response->getHeader(Header::ALLOW));
	}

}