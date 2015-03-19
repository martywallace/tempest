<?php namespace Tempest\Twig;

use \Twig_Environment;


/**
 * Defines a collection of Twig extensions.
 *
 * @author Marty Wallace.
 */
class Extensions
{

	public function __construct(Twig_Environment $environment)
	{
		foreach ($this->defineExtensions() as $handle => $callable)
		{
			// Add the defined extensions in the manner described in the subclass.
			$this->addExtension($environment, $handle, $callable);
		}
	}


	protected function defineExtensions()
	{
		return array();
	}


	protected function addExtension(Twig_Environment $environment, $handle, $callable)
	{
		// Describe how an extension is added in your subclass.
		// ...
	}

}