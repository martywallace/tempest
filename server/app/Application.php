<?php

namespace app;

use \tempest\base\Tempest;
use \app\handlers\Page;


class Application extends Tempest
{

	public function __construct()
	{
		$page = new Page();
		
		header("Content-type: {$page->getMime()}");
		echo $page->getOutput();
	}
	
}