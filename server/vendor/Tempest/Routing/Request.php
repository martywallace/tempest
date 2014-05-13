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


	private $data = array();


	public function __construct()
	{
		$this->data = array(
			self::GET => array_slice($_GET, 0),
			self::POST => array_slice($_POST, 0),
			self::FILE => array_slice($_FILES, 0),
			self::NAMED => array()
		);

		parent::__construct(APP_REQUEST_URI);
	}


	public function data($stack = null, $key = null)
	{
		if($stack === null) return $this->data;
		if($key === null) return $this->data[$stack];

		return in_array($this->data[$stack], $key) ? $this->data[$stack][$key] : null;
	}


	public function redirect($uri)
	{
		header("Location: " . PUBLIC_ROOT . $uri);
		exit;
	}

}