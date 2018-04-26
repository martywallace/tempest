<?php

namespace Tempest\Container;

use Tempest\Services\Service;
use Tempest\Events\ServiceEvent;
use Psr\Container\ContainerInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;

/**
 * A service container, used as an instance that can provide zero or more of its
 * own services.
 *
 * @author Ascension Web Development.
 */
class Container extends EventDispatcher implements ContainerInterface {

	/** @var string[] */
	private $services = [];

	/** @var Service[] */
	private $instantiated = [];

	/**
	 * Add a service to this container.
	 *
	 * @param string $service The fully qualified class name of the service to
	 * add to the container. This becomes its ID within the container.
	 *
	 * @throws ContainerException If a service with the same name already exists.
	 */
	public function add(string $service): void {
		if ($this->has($service)) {
			throw new ContainerException(sprintf(ContainerException::SERVICE_ALREADY_EXISTS, $service));
		}

		$this->services[] = $service;
	}

	/**
	 * Convenience method to add multiple services as once.
	 *
	 * @param string[] $services An array of services to add.
	 */
	public function addMany(array $services): void {
		foreach ($services as $service) {
			$this->add($service);
		}
	}

	/**
	 * Instantiate a service, making it available for future usage. This takes
	 * the fully qualified service class name {@link Container::add() that was provided}
	 * and creates a new instance of it.
	 *
	 * @param string $id The name of the service to instantiate.
	 *
	 * @throws ContainerException If an input service has already been booted.
	 */
	public function instantiate(string $id): void {
		if ($this->hasInstantiated($id)) {
			throw new ContainerException(sprintf(ContainerException::SERVICE_ALREADY_INSTANTIATED, $id));
		}

		$instance = new $id();

		$this->dispatch(ServiceEvent::BOOTED, new ServiceEvent($id, $instance));
		$this->instantiated[$id] = $instance;
	}

	/**
	 * Determine whether the container has a service with the specified ID.
	 *
	 * @param string $id The service ID.
	 *
	 * @return bool
	 */
	public function has($id): bool {
		return in_array($id, $this->services);
	}

	/**
	 * Determine whether the container has booted a service with the specified name.
	 *
	 * @param string $id The service name.
	 *
	 * @return bool
	 */
	public function hasInstantiated(string $id): bool {
		return array_key_exists($id, $this->instantiated);
	}

	/**
	 * Get a service. If the service has not been instantiated, it will be
	 * instantiated first.
	 *
	 * @param string $id The service ID.
	 *
	 * @return Service
	 *
	 * @throws NotFoundException If the service does not exist.
	 */
	public function get($id) {
		if (!$this->has($id)) {
			throw new NotFoundException(sprintf(NotFoundException::SERVICE_NOT_FOUND, $id));
		}

		if (!$this->hasInstantiated($id)) {
			$this->instantiate($id);
		}

		return $this->instantiated[$id];
	}

}