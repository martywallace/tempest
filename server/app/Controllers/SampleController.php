<?php namespace Controllers;

use Tempest\HTTP\Controller;
use Tempest\HTTP\Request;


class SampleController extends Controller
{

	public function setup(Request $request, Array $detail)
	{
		// Custom controller setup.
		// ...
	}


	public function index(Request $request, Array $detail)
	{
		// Handle request to SampleController::index.
		return $detail;
	}

}