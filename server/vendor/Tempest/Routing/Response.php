<?php namespace Tempest\Routing;

use Tempest\Base\Tempest;
use Tempest\Routing\Request;


/**
 * An application response - defines what should be sent back to the client.
 * @author Marty Wallace.
 */
class Response
{

	private $app;
	private $mime = MIME_HTML;


	/**
	 * Constructor.
	 * @param $app A reference to the core application class.
	 */
	public function __construct(Tempest $app)
	{
		$this->app = $app;
	}


	/**
	 * Sets up the basic response needs.
	 * Override for custom response setup logic.
	 */
	public function setup(Request $request){ /**/ }


	/**
	 * The default handler method is <code>index</code>.
	 */
	public function index(Request $request)
	{
		return "TODO.";
	}


	/**
	 * Returns the MIME value set by this response.
	 */
	public function getMime(){ return $this->mime; }


	/**
	 * Sets the MIME value to use for this response.
	 */
	public function setMime($value){ $this->mime = $value; }


	/**
	 * Returns the core application instance.
	 */
	public function getApp(){ return $this->app; }


	/**
	 * Returns the active <code>Config</code> instance.
	 */
	public function getConfig(){ return $this->app->getConfig(); }


	/**
	 * Returns the <code>Request</code> instance.
	 */
	public function getRequest(){ return $this->app->getRouter()->getRequest(); }

}