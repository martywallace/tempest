<?php namespace Tempest\HTTP;

use Tempest\Utils\Path;


/**
 * A request made to the application by the client.
 *
 * @author Marty Wallace.
 */
class Request extends Path
{

	private $router;
	private $data;
	private $format = '';


	/**
	 * Constructor.
	 */
	public function __construct()
	{
		$this->router = tempest()->getRouter();

		$requ = preg_replace('/\..*$/', '', REQUEST_URI);

		if(strlen($requ) !== strlen(REQUEST_URI))
		{
			// Using alternate format.
			preg_match('/\.(.+)$/', REQUEST_URI, $match);
			$this->format = $match[1];
		}

		parent::__construct(REQUEST_URI, Path::DELIMITER_LEFT);
	}


	/**
	 * Returns data associated with the Request.
	 * @param $stack string The stack to return data from. Can be GET, POST or NAMED.
	 * @param $key string The key holding the data within the selected stack.
	 * @param $default mixed A default value to return if the key did not exist on the selected stack.
	 * @return mixed The requested data, or the default value if it could not be found.
	 */
	public function data($stack = null, $key = null, $default = null)
	{
		$data = $this->getData();

		if($stack === null) return $data;
		if($key === null) return $data[$stack];

		return array_key_exists($key, $data[$stack]) ? $data[$stack][$key] : $default;
	}


	/**
	 * Redirect the Request to a new URL.
	 * @param $dest string The destination URL. Acts intelligently enough to redirect relative to the application root if an external URL is not provided.
	 * @param $status int The response code for the redirected request, e.g. 400 Bad Request or 302 Moved Permanently.
	 */
	public function redirect($dest, $status = 400)
	{
		header($_SERVER["HTTP_PROTOCOL"] . " $status", true, $status);

		if(preg_match('/^\w*:\/\//', $dest)) header("Location: " . $dest);
		else header("Location: " . $dest);

		exit;
	}


	/**
	 * Generates and returns the data stack for this Request.
	 */
	private function getData()
	{
		if($this->data === null)
		{
			$this->data = array(
				GET => array_slice($_GET, 0),
				POST => array_slice($_POST, 0),
				NAMED => array_slice(tempest()->getRouter()->getParams(), 0)
			);
		}

		return $this->data;
	}


	/**
	 * Shorthand for <code>if($format === 'some_format'){ ... }</code>.
	 */
	public function isFormat($format){ return $this->format === $format; }


	/**
	 * Returns the request method (GET, POST).
	 */
	public function getMethod(){ return strtolower($_SERVER["REQUEST_METHOD"]); }


	/**
	 * Returns the format (file extension) for this Request.
	 */
	public function getFormat(){ return $this->format; }

}