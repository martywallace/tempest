<?php namespace Tempest\Events;

use Exception;
use Symfony\Component\EventDispatcher\Event;

/**
 * An event dispatched when an exception occurs.
 *
 * @property-read Exception $exception The exception caught by this event.
 *
 * @author Marty Wallace
 */
class ExceptionEvent extends Event {

	const NAME = 'exception';

	/** @var Exception */
	private $_exception;

	public function __construct(Exception $exception) {
		$this->_exception = $exception;
	}

	public function __get($prop) {
		if ($prop === 'exception') return $this->_exception;

		return null;
	}

}