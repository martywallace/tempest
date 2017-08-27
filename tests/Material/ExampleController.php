<?php namespace Tempest\Tests\Material;

use Tempest\Http\Handler;

class ExampleController extends Handler {

	public function index() {
		$this->response->body('Test');
	}

}