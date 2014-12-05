<?php namespace Responses;

use Tempest\HTTP\Response;
use Tempest\HTTP\Request;


class AdaptivePage extends Response
{

	protected $name;


	public function setup(Request $r)
	{
		//
	}


	public function index(Request $request)
	{
		return tempest()->templates->render('home.html', []);
	}

}