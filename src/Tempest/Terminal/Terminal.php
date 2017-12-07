<?php namespace Tempest\Terminal;

use Tempest\Kernel\Input;
use Tempest\Kernel\Kernel;
use Symfony\Component\Console\Application;

/**
 * The terminal kernel manages the console application entrypoint.
 *
 * @author Marty Wallace
 */
class Terminal extends Kernel {

	/** @var Application */
	private $_console;

	public function __construct($config) {
		parent::__construct($config);

		$this->_console = new Application();

		if ($this->getConfig()) {
			$this->_console->addCommands($this->getConfig());
		}
	}

	/**
	 * Handle terminal input.
	 *
	 * @param Arguments|Input $input The terminal input.
	 */
	public function handle(Input $input) {
		$this->_console->run($input);
	}

	/**
	 * Retrieve the internal Symfony console application.
	 *
	 * @return Application
	 */
	public function getConsole() {
		return $this->_console;
	}

}