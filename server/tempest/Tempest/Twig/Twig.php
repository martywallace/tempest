<?php namespace Tempest\Twig;

use Tempest\IService;
use Tempest\Config;
use Tempest\Utils\Path;
use \Twig_Loader_Filesystem;
use \Twig_Environment;


/**
 * A service for rendering templates with Twig.
 *
 * @author Marty Wallace.
 */
class Twig implements IService
{

	private $loader;
	private $environment;
	private $functions;
	private $filters;
	private $config;


	public function __construct()
	{
		$this->config = new Config('twig');

		$this->loader = new Twig_Loader_Filesystem(array(
			Path::create(APP_ROOT . 'html', Path::DELIMITER_RETAIN)->rpad()
		));

		$this->environment = new Twig_Environment($this->loader, array(
			'debug' => tempest()->config('dev', false)
		));

		$this->functions = new Functions($this->environment);
		$this->filters = new Filters($this->environment);
	}


	/**
	 * Render a Twig template.
	 *
	 * @param string $file The file to load, relative to <code>/templates/</code>.
	 * @param array $context Data to pass to the template for rendering.
	 *
	 * @return null|TwigResponse
	 */
	public function render($file, $context = array())
	{
		if($this->loader->exists($file))
		{
			return new TwigResponse($this->environment->render($file, array_merge($context, array(
				'T' => tempest()->getServices(),
				'root' => tempest()->getRoot(),
				'config' => $this->config->data(),
				'request' => tempest()->getRouter()->getRequest()
			))));
		}

		return null;
	}


	/**
	 * Returns configuration data stored in <code>/config/twig.php</code>.
	 *
	 * @param string $prop The config property to get.
	 * @param mixed $fallback A fallback value to use if the property is not defined.
	 *
	 * @return mixed
	 */
	public function config($prop = null, $fallback = null)
	{
		return $this->config->data($prop, $fallback);
	}


	/**
	 * A reference to the Twig_Environment instance.
	 *
	 * @return Twig_Environment
	 */
	public function getEnvironment() { return $this->environment; }


	/**
	 * A reference to the Twig_Loader instance.
	 *
	 * @return Twig_Loader
	 */
	public function getLoader() { return $this->loader; }

}