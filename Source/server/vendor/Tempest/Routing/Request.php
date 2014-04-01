<?php namespace Tempest\Routing;

class Request
{

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


	public function redirect($uri, $local = true)
	{
		header("Location: " . ($local ? PUBL . $uri : $uri));
		exit();
	}


	public function getUri(){ return $this->uri; }

}