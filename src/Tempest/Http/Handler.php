<?php namespace Tempest\Http;

/**
 * A route handler - either in the form of middleware or a controller.
 *
 * @property-read Request $request The request made to trigger this handler.
 * @property-read Response $response The response that was generated to be sent at the end of the request.
 *
 * @author Marty Wallace
 */
abstract class Handler {

	/** @var Request */
	private $_request;

	/** @var Response */
	private $_response;

	/**
	 * Handler constructor.
	 *
	 * @internal
	 *
	 * @param Request $request
	 * @param Response $response
	 */
	public function __construct(Request $request, Response $response) {
		$this->_request = $request;
		$this->_response = $response;
	}

	public function __get($prop) {
		if ($prop === 'request') return $this->_request;
		if ($prop === 'response') return $this->_response;

		return null;
	}

	public function __isset($prop) {
		return $this->{$prop} !== null;
	}

}