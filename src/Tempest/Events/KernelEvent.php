<?php namespace Tempest\Events;

use Symfony\Component\EventDispatcher\Event;
use Tempest\Kernel\Kernel;
use Tempest\Kernel\Input;
use Tempest\Kernel\Output;

/**
 * Events related to the {@link Http HTTP Kernel}.
 *
 * @author Ascension Web Development
 */
class KernelEvent extends Event {

	const BOOTED = 'kernel.booted';
	const OUTPUT_READY = 'kernel.output-ready';

	/** @var Kernel */
	private $_kernel;

	/** @var Input */
	private $_input;

	/** @var Output */
	private $_output;

	public function __construct(Kernel $kernel, Input $input = null, Output $output = null) {
		$this->_kernel = $kernel;
		$this->_input = $input;
		$this->_output = $output;
	}

	/**
	 * @return Kernel
	 */
	public function getKernel() {
		return $this->_kernel;
	}

	/**
	 * @return Input
	 */
	public function getInput() {
		return $this->_input;
	}

	/**
	 * @return Output
	 */
	public function getOutput() {
		return $this->_output;
	}

}