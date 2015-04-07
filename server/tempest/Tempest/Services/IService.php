<?php namespace Tempest\Services;


/**
 * Defines an application service.
 *
 * @author Marty Wallace.
 */
interface IService
{

	/**
	 * Whether or not this service is accessible within Twig templates.
	 *
	 * @return bool
	 */
	public function isTwigAccessible();

}