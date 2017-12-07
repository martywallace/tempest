<?php namespace Tempest\Terminal;

use Tempest\Kernel\Input;
use Symfony\Component\Console\Input\ArgvInput;

/**
 * Input entered into the terminal when executing a command.
 *
 * @author Marty Wallace
 */
class Arguments extends ArgvInput implements Input {

	/**
	 * Capture input from $_SERVER['argv'] and create a new instance from it.
	 *
	 * @return static
	 */
	public static function capture() {
		return new static($_SERVER['argv']);
	}

}