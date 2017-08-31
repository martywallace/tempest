<?php namespace Tempest\Events;

use Symfony\Component\EventDispatcher\Event;
use Tempest\Services\Service;

/**
 * An event related to application services.
 *
 * @property-read string $serviceName The name of the service as provided when {@link App::services bound} to the
 * application.
 * @property-read Service $service The service associated with this event.
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

	public function __get($prop) {
		if ($prop === 'serviceName') return $this->_serviceName;
		if ($prop === 'service') return $this->_service;

		return null;
	}

}