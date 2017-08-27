<?php namespace Tempest\Tests\Material;

use Closure;
use Tempest\Http\Handler;

class ExampleMiddleware extends Handler {

	public function test(Closure $next) {
		$next();
	}

}