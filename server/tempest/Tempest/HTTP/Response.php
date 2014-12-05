<?php namespace Tempest\HTTP;

use Tempest\Tempest;
use Tempest\Output\BaseOutput;


/**
 * An application response - defines what should be sent back to the client.
 * @author Marty Wallace.
 */
class Response
{

	/**
	 * @var Tempest
	 */
	private $app;


	/**
	 * @param $name string The name of the Response class to create.
	 * @param Tempest $app A reference to the core application.
	 * @return Response The resulting Response class.
	 */
	public static function create($name, Tempest $app)
	{
		if(class_exists($name)) return new $name($app);
		else
		{
			trigger_error("Response class <code>$name</code> does not exist.");
			return null;
		}
	}


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
	 * @param $request Request The Request made to the application.
	 */
	public function setup(Request $request){ /**/ }


	/**
	 * Finalize output that this response will send to the client.
	 * @param string|BaseOutput $output The output to finalize.
	 * @return string|BaseOutput The finalized response data.
	 */
	public function finalize($output = null)
	{
		return $output;
	}


	/**
	 * The default handler method is <code>index</code>.
	 * @param $request Request The Request made to the application.
	 */
	public function index(Request $request){ /**/ }


	/**
	 * The main application.
	 * @return Tempest
	 */
	public function getApp(){ return $this->app; }


	/**
	 * The current Request.
	 * @return Request
	 */
	public function getRequest(){ return $this->app->getRouter()->getRequest(); }

}