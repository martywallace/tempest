<?php namespace Tempest\Twig;

use Tempest\Output\HTMLOutput;
use Tempest\Tempest;
use Tempest\Service;
use \Twig_Loader_Filesystem;
use \Twig_Environment;


class Twig extends Service
{

	private $loader;
	private $environment;


	public function __construct(Tempest $app)
	{
		parent::__construct($app);

		$this->loader = new Twig_Loader_Filesystem(array(APP_ROOT . 'templates/'));
		$this->environment = new Twig_Environment($this->loader);
	}


	public function render($file, $context = array())
	{
		return new HTMLOutput($this->environment->render($file, array_merge($context, array(
			'tempest' => tempest()->getServices(),
			'title' => tempest()->config('title')
		))));
	}

}