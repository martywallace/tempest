<?php namespace Tempest\HTTP;

use Tempest\Tempest;


/**
 * An application controller - defines what should be sent back to the client.
 * @author Marty Wallace.
 */
class Controller
{

	/**
	 * @var Tempest
	 */
	private $app;


	/**
	 * @param $name string The name of the Controller class to create.
	 * @param Tempest $app A reference to the core application.
	 * @return Controller The resulting Controller class.
	 */
	public static function create($name, Tempest $app)
	{
		if(class_exists($name)) return new $name($app);
		else
		{
			trigger_error("Controller <code>$name</code> does not exist.");
			return null;
		}
	}


	/**
	 * Constructor.
	 *
	 * @param $app Tempest A reference to the core application class.
	 */
	public function __construct(Tempest $app)
	{
		$this->app = $app;
	}


	/**
	 * Sets up the basic response needs.
	 * Override for custom response setup logic.
	 *
	 * @param Request $request The Request made to the application.
	 * @param array $vars Custom variables defined alongside the route.
	 */
	public function setup(Request $request, Array $vars){ /**/ }


	/**
	 * Finalize output that this response will send to the client.
	 *
	 * @param string|Response $output The output to finalize.
	 *
	 * @return string|Response The finalized response data.
	 */
	public function finalize($output = null)
	{
		return $output;
	}


	/**
	 * The default handler method is <code>index</code>.
	 *
	 * @param $request Request The Request made to the application.
	 * @param array $vars Custom variables defined alongside the route.
	 */
	public function index(Request $request, Array $vars){ /**/ }


	/**
	 * The main application.
	 *
	 * @return Tempest
	 */
	public function getApp(){ return $this->app; }


	/**
	 * The current Request.
	 *
	 * @return Request
	 */
	public function getRequest(){ return tempest()->getRouter()->getRequest(); }

}