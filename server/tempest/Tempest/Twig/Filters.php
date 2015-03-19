<?php namespace Tempest\Twig;

use \Twig_Environment;
use \Twig_SimpleFilter;


class Filters extends Extensions
{

	protected function defineExtensions()
	{
		return array(
			'pluck' => array($this, 'pluck')
		);
	}


	protected function addExtension(Twig_Environment $environment, $handle, $callable)
	{
		$environment->addFilter(new Twig_SimpleFilter($handle, $callable));
	}


	/**
	 * Pluck the value of a specified property from each item in an input array and return the
	 * result array.
	 *
	 * @param array $input The input array.
	 * @param string $property The property to pluck from each item in the input array.
	 *
	 * @return array
	 */
	public function pluck($input, $property)
	{
		$output = array();
		foreach ($input as $object)
		{
			if (is_array($object) && array_key_exists($property, $object)) $output[] = $object[$property];
			if (is_object($object) && property_exists($object, $property)) $output[] = $object->{$property};
		}

		return $output;
	}

}