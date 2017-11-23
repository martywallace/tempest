<?php namespace Tempest\Kernel;

use Closure;
use Exception;
use Tempest\App;
use Tempest\Utility;
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
	 * Prepares data to dump to the kernel.
	 *
	 * @param mixed $data The data to dump.
	 * @param string $format The format to use.
	 *
	 * @return Output|string
	 */
	public function dump($data, $format = App::DUMP_FORMAT_PRINT_R) {
		if ($format === App::DUMP_FORMAT_JSON) {
			return json_encode($data, JSON_PRETTY_PRINT);
		}

		return Utility::buffer(function() use ($data, $format) {
			if ($format === App::DUMP_FORMAT_PRINT_R) print_r($data);
			else if ($format === App::DUMP_FORMAT_VAR_DUMP) var_dump($data);
			else throw new Exception('Unknown dump format "' . $format . '".');
		});
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

	/**
	 * Handle an exception thrown by this kernel.
	 *
	 * @param Input $input The kernel input causing the exception.
	 * @param Output $output The kernel output to potentially bind exception information to.
	 * @param Exception $exception The exception thrown.
	 */
	protected abstract function handleException(Input $input, Output $output, Exception $exception);

}