<?php namespace Tempest\Tests\Material;

use Tempest\Http\Handler;
use Tempest\Http\Request;
use Tempest\Http\Response;

class ExampleController extends Handler {

	public function index(Request $request, Response $response) {
		$response->text('Test');
	}

	public function json(Request $request, Response $response) {
		$response->json(['test' => 10]);
	}

	public function getAll(Request $request, Response $response) {
		$response->json([]);
	}

	public function getDogs(Request $request, Response $response) {
		$response->json([]);
	}

	public function createDog(Request $request, Response $response) {
		$response->json([]);
	}

	public function getCats(Request $request, Response $response) {
		$response->json([]);
	}

	public function createCat(Request $request, Response $response) {
		$response->json([]);
	}

	public function convertJson(Request $request, Response $response) {
		$response->json($request->data());
	}

}