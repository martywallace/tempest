<?php namespace App;

use Tempest\Base\Tempest;


class Application extends Tempest
{
	
	protected function setup()
	{
		header("Content-type: text/plain");

		$this->router->register(array(
			"index" => "Test",
			"a/b/c" => "Test",
			"a/b/[c]" => "Test",
			"a/[b]/[c]" => "Test"
		));
	}

}