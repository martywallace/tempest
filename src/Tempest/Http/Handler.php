<?php namespace Tempest\Http;

use Exception;

/**
 * A route handler - either in the form of middleware or a controller.
 *
 * @property-read Request $request The request made to trigger this handler.
 * @property-read Response $response The response that was generated to be sent at the end of the request.
 *
 * @author Marty Wallace
 */
abstract class Handler {

	/**
	 * Creates a callable from a method within this class.
	 *
	 * @param string $method The class method to resolve to.
	 * @param array $options Optional options to attach to the handler when it is instantiated.
	 *
	 * @return array
	 *
	 * @throws Exception if the method does not exist within this class.
	 */
	public static function do($method = 'index', array $options = []) {
		if (!method_exists(static::class, $method)) {
			throw new Exception('HTTP handler "' . static::class . '" does not define a method "' . $method . '".');
		}

		return [static::class, $method, $options];
	}

	/** @var Request */
	private $_request;

	/** @var Response */
	private $_response;

	/** @var array */
	private $_options;

	/**
	 * Handler constructor.
	 *
	 * @internal
	 *
	 * @param Request $request
	 * @param Response $response
	 * @param array $options
	 */
	public function __construct(Request $request, Response $response, array $options = []) {
		$this->_request = $request;
		$this->_response = $response;
		$this->_options = $options;
	}

	public function __get($prop) {
		if ($prop === 'request') return $this->_request;
		if ($prop === 'response') return $this->_response;
		if ($prop === 'options') return $this->_options;

		return null;
	}

	public function __isset($prop) {
		return $this->{$prop} !== null;
	}

}