<?php namespace Tempest\Events;

use Symfony\Component\EventDispatcher\Event;
use Tempest\Http\{Http, Request, Response};

/**
 * Events related to the {@link Http HTTP Kernel}.
 *
 * @property-read Http $http The HTTP Kernel trigger this event.
 * @property-read Request $request The request associated with this event.
 * @property-read Response $response The response associated with this event.
 *
 * @author Marty Wallace
 */
class HttpKernelEvent extends Event {

	const BOOTED = 'app.http.booted';
	const RESPONSE_READY = 'app.http.responseready';

	/** @var Http */
	private $_http;

	/** @var Request */
	private $_request;

	/** @var Response */
	private $_response;

	public function __construct(Http $http, Request $request = null, Response $response = null) {
		$this->_http = $http;
		$this->_request = $request;
		$this->_response = $response;
	}

	public function __get($prop) {
		if ($prop === 'http') return $this->_http;
		if ($prop === 'request') return $this->_request;
		if ($prop === 'response') return $this->_response;

		return null;
	}

}