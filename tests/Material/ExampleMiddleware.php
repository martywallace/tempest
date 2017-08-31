<?php namespace Tempest\Tests\Material;

use Closure;
use Tempest\Http\Handler;
use Tempest\Http\Request;
use Tempest\Http\Response;

class ExampleMiddleware extends Handler {

	public function test(Request $request, Response $response, Closure $next) {
		$next();
	}

}