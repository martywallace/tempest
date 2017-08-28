<?php namespace Tempest\Tests\Material;

use Tempest\Http\Handler;

class ExampleController extends Handler {

	public function index() {
		$this->response->text('Test');
	}

	public function json() {
		$this->response->json(['test' => 10]);
	}

}