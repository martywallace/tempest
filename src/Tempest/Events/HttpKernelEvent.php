<?php namespace Tempest\Events;

use Symfony\Component\EventDispatcher\Event;
use Tempest\Http\Http;

/**
 * Events related to the {@link Http HTTP Kernel}.
 *
 * @property-read Http $http The HTTP Kernel trigger this event.
 *
 * @author Marty Wallace
 */
class HttpKernelEvent extends Event {

	const BOOTED = 'app.http.booted';

	/** @var Http */
	private $_http;

	public function __construct(Http $http) {
		$this->_http = $http;
	}

	public function __get($prop) {
		if ($prop === 'http') return $this->_http;

		return null;
	}

}