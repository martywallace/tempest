<?php namespace App;

use Tempest\Base\Tempest;


class Application extends Tempest
{
	
	protected function setup()
	{
		$this->router->register(array(
			"/" => "Page"
		));
	}

}