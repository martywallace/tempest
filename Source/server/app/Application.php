<?php

use Tempest\Base\Tempest;


class Application extends Tempest
{
	
	protected function setup()
	{
		session_start();

		if(!isset($_SESSION["visits"]))
		{
			$_SESSION["visits"] = 0;
		}

		$_SESSION["visits"] ++;
	}

}