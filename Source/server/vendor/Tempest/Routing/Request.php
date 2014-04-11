<?php namespace Tempest\Routing;

use Tempest\Utils\Path;


/**
 * A request made to the application by a user.
 * @author Marty Wallace.
 */
class Request extends Path
{

	const GET = 'get';
	const POST = 'post';
	const FILE = 'file';
	const NAMED = 'named';
	const HASH = 'hash';


	public function __construct()
	{
		$uri = '/' . path_normalize($_SERVER["REQUEST_URI"], '/');

		if(DIR_BASE !== '/')
		{
			// Need to trim the application root off the front of the request.
			// Do not do this if the application root is the server root, or the request URI will
			// be stripped of all slashes and obviously break.
			$uri = str_needle_remove(DIR_BASE, $uri);
		}

		parent::__construct($uri);
	}


	public function data($stack, $key = null)
	{
		// TODO.
		return null;
	}


	public function redirect($uri)
	{
		header("Location: " . DIR_BASE . $uri);
		exit();
	}

}