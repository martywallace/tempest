<?php namespace Tempest\Http;

use Exception;

/**
 * A route to be handled by the HTTP kernel.
 *
 * @author Marty Wallace
 */
class Route extends Resource {

	const MODE_UNDETERMINED = 0;
	const MODE_TEMPLATE = 1;
	const MODE_CONTROLLER = 2;

	/** @var string|string[] */
	private $_method;

	/** @var string */
	private $_template;

	/** @var mixed[] */
	private $_controller;

	/**
	 * Route constructor.
	 *
	 * @param string|string[] $method
	 * @param string $uri
	 */
	public function __construct($method, $uri) {
		parent::__construct($uri);
		$this->_method = $method;
	}

	/**
	 * Get the HTTP methods that triggers this route.
	 *
	 * @return string|string[]
	 */
	public function getMethod() {
		return $this->_method;
	}

	/**
	 * Get the route mode (e.g. whether this route renders a template, calls a controller, etc).
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
	 * Get the template attached to this route.
	 *
	 * @return string
	 */
	public function getTemplate() {
		return $this->_template;
	}

	/**
	 * Get the controller attached to this route.
	 *
	 * @return mixed[]
	 */
	public function getController() {
		return $this->_controller;
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
	public function render($name) {
		if (!empty($this->_template)) throw new Exception('This route already triggers a template.');
		if (!empty($this->_controller)) throw new Exception('A route cannot trigger both a template and a controller.');

		$this->_template = $name;

		return $this;
	}

	/**
	 * Attach a controller method to call when this route is matched.
	 *
	 * @param array $action The action to perform provided by your controller class.
	 *
	 * @return $this
	 *
	 * @throws Exception I the route already handles a controller.
	 * @throws Exception If the route already handles a template.
	 */
	public function controller(array $action) {
		if (!empty($this->_controller)) throw new Exception('This route already triggers a controller.');
		if (!empty($this->_template)) throw new Exception('A route cannot trigger both a template and a controller.');

		$this->_controller = $action;

		return $this;
	}

}