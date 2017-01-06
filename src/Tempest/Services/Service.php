<?php namespace Tempest\Services;


/**
 * An application service, attached to the main application to provide bundles of functionality.
 *
 * @package Tempest\Services
 * @author Marty Wallace
 */
interface Service {

	/**
	 * Contains service setup code. Run the first time the service is accessed via the main application.
	 */
	function setup();

}