<?php namespace Tempest\Kernel;

use Closure;
use Exception;
use Tempest\App;
use Symfony\Component\EventDispatcher\EventDispatcher;

/**
 * A kernel is responsible for capturing a specific channel of application input (e.g. over HTTP or via the console) and
 * generating an appropriate output.
 *
 * @author Marty Wallace
 */
abstract class Kernel extends EventDispatcher {

	/** @var mixed */
	private $_config = null;

	/**
	 * Kernel constructor.
	 *
	 * @param Closure|string $config Kernel configuration, provided either as a Closure that accepts the Kernel as its
	 * only argument or as a string pointing to a file that returns said closure, relative to the
	 * {@link App::root application root}.
	 *
	 * @throws Exception If the provided configuration does not resolve to a callable.
	 */
	public function __construct($config) {
		if (!empty($config)) {
			if (is_string($config)) {
				// Load external configuration.
				$config = require App::get()->root . DIRECTORY_SEPARATOR . $config;
			}

			if (!is_callable($config)) {
				throw new Exception('Kernel configuration must resolve to a callable.');
			}

			$this->_config = $config($this);
		}
	}

	/**
	 * Get the resolved kernel configuration.
	 *
	 * @return mixed
	 */
	public function getConfig() {
		return $this->_config;
	}

	/**
	 * Handles kernel input and generates appropriate output.
	 *
	 * @param Input $input The input to handle.
	 *
	 * @return Output
	 */
	public abstract function handle(Input $input);

}