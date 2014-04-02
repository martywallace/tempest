<?php namespace Tempest\Base;

use Tempest\Routing\Request;


class Tempest
{

	private $request;

	
	public function __construct()
	{
		header("Content-type: text/plain");

		//$this->request = new Request();
		//echo $this->request->getUri();

		//echo "<br>working";
	}

}