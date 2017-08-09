<?php namespace Tempest\Http;

use Exception;

/**
 * A route to be handled by the HTTP kernel.
 *
 * @property-read string|string[] $method The HTTP method associated with this route.
 * @property-read string $uri The URI this route handles.
 *
 * @author Marty Wallace
 */
class Route {

	const MODE_UNDETERMINED = 0;
	const MODE_TEMPLATE = 1;
	const MODE_CONTROLLER = 2;

	/** @var string|string[] */
	private $_method;

	/** @var string */
	private $_uri;

	/** @var string */
	private $_template;

	/** @var string[] */
	private $_controller;

	/**
	 * Route constructor.
	 *
	 * @param string|string[] $method
	 * @param string $uri
	 */
	public function __construct($method, $uri) {
		$this->_method = $method;
		$this->_uri = $uri;
	}

	public function __get($prop) {
		if ($prop === 'method') return $this->_method;
		if ($prop === 'uri') return $this->_uri;

		return null;
	}

	/**
	 * Get the route mode (e.g. whether this route renders a template, calls a controller, etc).
	 *
	 * @internal
	 *
	 * @see Route::MODE_UNDETERMINED
	 * @see Route::MODE_TEMPLATE
	 * @see Route::MODE_CONTROLLER
	 *
	 * @return int
	 */
	public function getMode() {
		if (!empty($this->_template)) return self::MODE_TEMPLATE;
		else if (!empty($this->_controller)) return self::MODE_CONTROLLER;

		return self::MODE_UNDETERMINED;
	}

	/**
	 * Attach a template to render when this route is matched.
	 *
	 * @param string $name The name of the template to render.
	 *
	 * @return $this
	 *
	 * @throws Exception If the route already handles a controller.
	 * @throws Exception If the route already handles a template.
	 */
	public function template($name) {
		if (!empty($this->_template)) throw new Exception('This route already triggers a template.');
		if (!empty($this->_controller)) throw new Exception('A route cannot trigger both a template and a controller.');

		$this->_template = $name;

		return $this;
	}

	/**
	 * Attach a controller method to call when this route is matched.
	 *
	 * @param string $class The name of the controller class.
	 * @param string $method The name of the controller method within the class.
	 *
	 * @return $this
	 *
	 * @throws Exception I the route already handles a controller.
	 * @throws Exception If the route already handles a template.
	 */
	public function controller($class, $method = 'index') {
		if (!empty($this->_controller)) throw new Exception('This route already triggers a controller.');
		if (!empty($this->_template)) throw new Exception('A route cannot trigger both a template and a controller.');

		$this->_controller = [$class, $method];

		return $this;
	}

}