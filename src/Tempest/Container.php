<?php namespace Tempest;

use Exception;
use Tempest\Events\ServiceEvent;
use Symfony\Component\EventDispatcher\EventDispatcher;

/**
 * A service container, used as an instance that can provide zero or more of its own services.
 *
 * @author Marty Wallace.
 */
abstract class Container extends EventDispatcher {

	/** @var string[] */
	private $_services = [];

	/** @var Service[] */
	private $_serviceInstances = [];

	protected function __construct() {
		$this->_services = array_merge(
			$this->_services,
			$this->services()
		);
	}

	public function __get($prop) {
		if ($this->hasService($prop)) {
			return $this->getService($prop);
		}

		return null;
	}

	public function __isset($prop) {
		return $this->hasService($prop);
	}

	/**
	 * Add a service to this container.
	 *
	 * @param string $name The name used to reference the service within the container.
	 * @param string $service The class name of the service to add.
	 *
	 * @throws Exception If a service with the same name already exists.
	 */
	protected function addService($name, $service) {
		if ($this->hasService($name)) {
			throw new Exception('Service "' . $service . '" has already been added to this container.');
		}

		$this->_services[$name] = $service;
	}

	/**
	 * Bulk add services to this container.
	 *
	 * @param string[] $services The list of services to add.
	 */
	public function addServices(array $services) {
		foreach ($services as $name => $service) {
			$this->addService($name, $service);
		}
	}

	/**
	 * Force boot a service, instantiating it for future usage.
	 *
	 * @param string $name The name of the service to boot.
	 *
	 * @throws Exception If an input service has already been booted.
	 */
	public function bootService($name) {
		if ($this->hasBootedService($name)) {
			throw new Exception('Service "' . $name . '" has already been booted.');
		}

		$instance = new $this->_services[$name]();

		$this->dispatch(ServiceEvent::BOOTED, new ServiceEvent($name, $instance));
		$this->_serviceInstances[$name] = $instance;
	}

	/**
	 * Force boot multiple services, instantiating them for future usage.
	 *
	 * @param string[] $names The names of the services to boot.
	 *
	 * @throws Exception If an input service has already been booted.
	 */
	protected function bootServices(array $names) {
		if (!is_array($names)) $names = [$names];

		foreach ($names as $name) {
			$this->bootService($name);
		}
	}

	/**
	 * Determine whether the container has a service with the specified name.
	 *
	 * @param string $name The service name.
	 *
	 * @return bool
	 */
	public function hasService($name) {
		return array_key_exists($name, $this->_services);
	}

	/**
	 * Determine whether the container has booted a service with the specified name.
	 *
	 * @param string $name The service name.
	 *
	 * @return bool
	 */
	public function hasBootedService($name) {
		return array_key_exists($name, $this->_serviceInstances);
	}

	/**
	 * Get a service. If the service has not been booted, it will be booted first.
	 *
	 * @param string $name The service name.
	 *
	 * @return Service
	 *
	 * @throws Exception If the service does not exist.
	 */
	public function getService($name) {
		if (!$this->hasService($name)) throw new Exception('Service "' . $name . '" does not exist.');
		if (!$this->hasBootedService($name)) $this->bootService($name);

		return $this->_serviceInstances[$name];
	}

	/**
	 * Declare all services to be bound.
	 *
	 * @return string[]
	 */
	abstract protected function services();

}