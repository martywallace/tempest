<?php namespace Tempest\Events;

use Symfony\Component\EventDispatcher\Event;
use Tempest\Kernel\Kernel;
use Tempest\Kernel\Input;
use Tempest\Kernel\Output;

/**
 * Events related to the {@link Http HTTP Kernel}.
 *
 * @property-read Kernel $kernel The kernel triggering this event.
 * @property-read Input $input The kernel input.
 * @property-read Output $output The kernel output.
 *
 * @author Marty Wallace
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

	public function __get($prop) {
		if ($prop === 'kernel') return $this->_kernel;
		if ($prop === 'input') return $this->_input;
		if ($prop === 'output') return $this->_output;

		return null;
	}

}