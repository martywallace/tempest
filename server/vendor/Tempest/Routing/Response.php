<?php namespace Tempest\Routing;

use Tempest\Base\Tempest;


/**
 * An application response - defines what should be sent back to the client.
 * @author Marty Wallace.
 */
class Response
{

	private $app;


	/**
	 * Constructor.
	 * @param $app Tempest A reference to the core application class.
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
	 * Finalize output that this response will send to the client.
	 * @param string|Output $output The output to finalize.
	 * @return string|Output The finalized response data.
	 */
	public function finalize($output = null)
	{
		return $output;
	}


	/**
	 * The default handler method is <code>index</code>.
	 */
	public function index(Request $request){ /**/ }


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