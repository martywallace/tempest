<?php namespace Tempest\Twig;

use \Twig_Environment;
use \Twig_SimpleFilter;


class Filters extends Extensions
{

	protected function defineExtensions()
	{
		return array(
			'pluck' => 'Tempest\Utils\ArrayUtil::pluck',
			'hyphenate' => 'Tempest\Utils\StringUtil::hyphenate'
		);
	}


	protected function addExtension(Twig_Environment $environment, $handle, $callable)
	{
		$environment->addFilter(new Twig_SimpleFilter($handle, $callable));
	}

}