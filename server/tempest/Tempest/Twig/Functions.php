<?php namespace Tempest\Twig;

use Tempest\Utils\Path;
use \Twig_Environment;
use \Twig_SimpleFunction;


class Functions extends Extensions
{

	protected function defineExtensions()
	{
		return array(
			'link' => array($this, 'link')
		);
	}


	protected function addExtension(Twig_Environment $environment, $handle, $callable)
	{
		$environment->addFunction(new Twig_SimpleFunction($handle, $callable));
	}


	/**
	 * Creates a link relative to the application root defined in the configuration. If no root is
	 * defined in the config file, it is considered to be a single forward slash.
	 *
	 * @param string $value The input path.
	 *
	 * @return Path
	 */
	public function link($value)
	{
		return Path::create($value)
			->prepend(tempest()->getRoot());
	}

}