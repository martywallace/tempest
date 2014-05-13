<?php namespace App;

use Tempest\Base\Tempest;


class Application extends Tempest
{
	
	protected function setup()
	{
		$this->router->register(array(
			"/" => "Test",
			"/a/b/c" => "Test",
			"/a/b/[c]" => "Test",
			"/a/[b]/[c]" => "Test",
			"/test" => "Test"
		));
	}

}