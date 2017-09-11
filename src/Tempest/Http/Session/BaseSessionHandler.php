<?php namespace Tempest\Http\Session;

use SessionHandlerInterface;
use Tempest\Http\Request;

/**
 * The base class for all application session handling.
 *
 * @author Marty Wallace
 */
abstract class BaseSessionHandler implements SessionHandlerInterface {

	/** @var Request */
	private $_request;

	/**
	 * Attaches a HTTP request to this session so that it may draw useful information from it (e.g. remote IP address).
	 *
	 * @param Request $request The request to attach.
	 */
	public function attachRequest(Request $request) {
		$this->_request = $request;
	}

	public function open($path, $name) {
		return true;
	}

	public function close() {
		return true;
	}

	/**
	 * Get the request attached to this session.
	 *
	 * @return Request
	 */
	public function getRequest() {
		return $this->_request;
	}

}