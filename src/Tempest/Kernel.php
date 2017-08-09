<?php namespace Tempest;

/**
 * A kernel is responsible for capturing a specific channel of application input (e.g. over HTTP or via the console) and
 * generating an appropriate output.
 *
 * @author Marty Wallace
 */
abstract class Kernel {

	/**
	 * Produce a new kernel instance.
	 *
	 * @return static
	 */
	public static function make() {
		return new static();
	}

	protected function __construct() {
		//
	}

}