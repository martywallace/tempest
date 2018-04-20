<?php namespace Tempest\Events;

use Throwable;
use Symfony\Component\EventDispatcher\Event;

/**
 * An event dispatched when an exception occurs.
 *
 * @author Marty Wallace
 */
class ExceptionEvent extends Event {

	const EXCEPTION = 'exception';

	/** @var Throwable */
	private $_exception;

	public function __construct(Throwable $exception) {
		$this->_exception = $exception;
	}

	/**
	 * @return Throwable
	 */
	public function getException() {
		return $this->_exception;
	}

}