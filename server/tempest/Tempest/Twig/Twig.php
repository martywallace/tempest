<?php namespace Tempest\Twig;

use Tempest\IService;
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


	public function __construct()
	{
		$this->loader = new Twig_Loader_Filesystem(array(APP_ROOT . 'templates/'));
		$this->environment = new Twig_Environment($this->loader);
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
				'tempest' => tempest()->getServices(),
				'request' => tempest()->getRouter()->getRequest(),
				'title' => tempest()->config('title')
			))));
		}

		return null;
	}

}