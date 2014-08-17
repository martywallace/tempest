<?php namespace Tempest\Routing;

use Tempest\Utils\Path;


/**
 * A request made to the application by the client.
 * @author Marty Wallace.
 */
class Request extends Path
{

	private $router;
	private $data;
	private $format = '';


	/**
	 * Constructor.
	 * @param $router The Router managing this Request.
	 */
	public function __construct($router)
	{
		$this->router = $router;

		$requ = preg_replace('/\..*$/', '', APP_REQUEST_URI);

		if(strlen($requ) !== strlen(APP_REQUEST_URI))
		{
			// Using alternate format.
			preg_match('/\.(.+)$/', APP_REQUEST_URI, $match);
			$this->format = $match[1];
		}

		parent::__construct($requ);
	}


	/**
	 * Returns data associated with the Request.
	 * @param $stack The stack to return data from. Can be GET, POST or NAMED.
	 * @param $key The key holding the data within the selected stack.
	 * @param $default A default value to return if the key did not exist on the selected stack.
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
	 * @param $dest The destination URL. Acts intelligently enough to redirect relative to the application
	 *			 root if an external URL is not provided.
	 * @param $status The response code for the redirected request, e.g. 400 Bad Request or 302 Moved Permanently.
	 */
	public function redirect($dest, $status = 400)
	{
		if(preg_match('/^\w*:\/\//', $dest)) header("Location: " . $dest);
		else
		{
			header($_SERVER["HTTP_PROTOCOL"] . " $status", true, $status);
			header("Location: " . PUB_ROOT . $dest);
		}

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
				NAMED => array_slice($this->router->getParams(), 0)
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