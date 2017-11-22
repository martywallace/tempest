<?php namespace Tempest\Events;

use Symfony\Component\EventDispatcher\Event;
use Tempest\Services\Service;

/**
 * An event related to application services.
 *
 * @author Marty Wallace
 */
class ServiceEvent extends Event {

	const BOOTED = 'service.booted';

	/** @var string */
	private $_serviceName;

	/** @var Service */
	private $_service;

	public function __construct($name, Service $service) {
		$this->_serviceName = $name;
		$this->_service = $service;
	}

	/**
	 * @return string
	 */
	public function getServiceName() {
		return $this->_serviceName;
	}

	/**
	 * @return Service
	 */
	public function getService() {
		return $this->_service;
	}

}