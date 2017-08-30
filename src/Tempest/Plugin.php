<?php namespace Tempest;

use Tempest\Http\{Http, Route, Group};

/**
 * An application plugin, providing an entire suite of additional functionality with its own services, HTTP routes,
 * templates and so on.
 *
 * @author Marty Wallace
 */
abstract class Plugin {

	/**
	 * The plugin developer.
	 *
	 * @return string
	 */
	abstract public function developer();

	/**
	 * The plugin version.
	 *
	 * @return string
	 */
	abstract public function version();

	/**
	 * General plugin setup.
	 *
	 * @return mixed
	 */
	abstract protected function setup();

	/**
	 * Plugin configuration.
	 *
	 * @param Environment $env The application environment.
	 *
	 * @return array
	 */
	protected function config(Environment $env) {
		return [];
	}

	/**
	 * Attaches plugin routes and middleware to the application in the case of the HTTP kernel being used.
	 *
	 * @param Http $http The HTTP kernel.
	 *
	 * @return Route[]|Group[]
	 */
	protected function http(Http $http) {
		return [];
	}

}