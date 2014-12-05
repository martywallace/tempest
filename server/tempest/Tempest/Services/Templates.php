<?php namespace Tempest\Services;

use Tempest\Output\HTMLOutput;
use Tempest\Tempest;
use \Twig_Loader_Filesystem;
use \Twig_Environment;


class Templates extends Service
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
		return new HTMLOutput($this->environment->render($file, $context));
	}


	public function getTwig(){ return $this->environment; }
	public function getServiceName(){ return 'templates'; }

}