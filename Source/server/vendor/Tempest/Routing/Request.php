<?php namespace Tempest\Routing;


/**
 * A request made to the application by a user.
 * @author Marty Wallace.
 */
class Request
{

	const POST = 'post';
	const GET = 'get';
	const NAMED = 'named';
	const HASH = 'hash';


	private $uri;


	public function __construct()
	{
		$this->uri = '/' . normalize_path($_SERVER["REQUEST_URI"], '/');

		if(PUBL !== '/')
		{
			// Need to trim the application root off the front of the request.
			// Do not do this if the application root is the server root, or the request URI will
			// be stripped of all slashes and obviously break.
			$this->uri = str_replace(PUBL, '', $this->uri);
		}

		echo $this->uri;
	}


	public function data($stack, $key = null)
	{
		if($key === null)
		{
			// Returns entire stack.
			//
		}

		switch($stack)
		{
			default: return null; break;

			case self::POST: return $_POST[$key]; break;
			case self::GET: return $_GET[$key]; break;
		}

		return null;
	}


	public function redirect($uri)
	{
		header("Location: " . PUBL . $uri);
		exit();
	}


	public function getUri(){ return $this->uri; }

}