<?php namespace Tempest\Services;

/**
 * An application service, attached to the main application to provide bundles of functionality.
 *
 * @property-read bool $setup Whether or not this service has been set up yet.
 *
 * @package Tempest\Services
 * @author Marty Wallace
 */
abstract class Service {

	/** @var bool */
    private $_setup = false;

	public function __get($prop) {
		if ($prop === 'setup') return $this->_setup;

		return null;
	}

	/**
	 * Proxy call to set up the component.
	 */
	public function runSetup() {
		if (!$this->_setup) {
			$this->setup();
			$this->_setup = true;
		}
	}

	/**
	 * Set up this service. This method is called internally the first time it is used. By placing setup code in here
	 * vs the class constructor, we can avoid initializing services for requests where they are not needed.
	 */
	protected function setup() {
		//
	}

}