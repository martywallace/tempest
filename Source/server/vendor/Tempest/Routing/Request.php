<?php namespace Tempest\Routing;

use Tempest\Utils\Path;


/**
 * A request made to the application by a user.
 * @author Marty Wallace.
 */
class Request extends Path
{

	private $router;
	private $data;


	public function __construct($router)
	{
		$this->router = $router;
		parent::__construct(APP_REQUEST_URI);
	}


	public function data($stack = null, $key = null, $default = null)
	{
		$data = $this->getData();

		if($stack === null) return $data;
		if($key === null) return $data[$stack];

		return array_key_exists($key, $data[$stack]) ? $data[$stack][$key] : $default;
	}


	public function redirect($uri)
	{
		header("Location: " . PUBLIC_ROOT . $uri);
		exit;
	}


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

}