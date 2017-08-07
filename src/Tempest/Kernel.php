<?php namespace Tempest;

/**
 * A kernel is responsible for capturing a specific channel of application input (e.g. over HTTP or via the console) and
 * generating an appropriate output.
 *
 * @author Marty Wallace
 */
abstract class Kernel {

	/** @var App */
	protected $app;

	/**
	 * Produce a new kernel instance.
	 *
	 * @param App $app The application associated with this Kernel.
	 *
	 * @return static
	 */
	public static function make(App $app) {
		return new static($app);
	}

	/**
	 * Kernel constructor.
	 *
	 * @param App $app
	 */
	private function __construct(App $app) {
		$this->app = $app;
	}

}