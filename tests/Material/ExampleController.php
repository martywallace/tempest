<?php namespace Tempest\Tests\Material;

use Tempest\Http\Handler;

class ExampleController extends Handler {

	public function index() {
		$this->response->text('Test');
	}

	public function json() {
		$this->response->json(['test' => 10]);
	}

	public function getAll() {
		$this->response->json([]);
	}

	public function getDogs() {
		$this->response->json([]);
	}

	public function createDog() {
		$this->response->json([]);
	}

	public function getCats() {
		$this->response->json([]);
	}

	public function createCat() {
		$this->response->json([]);
	}

}