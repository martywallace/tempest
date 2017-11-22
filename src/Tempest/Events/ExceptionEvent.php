<?php namespace Tempest\Events;

use Exception;
use Symfony\Component\EventDispatcher\Event;

/**
 * An event dispatched when an exception occurs.
 *
 * @author Marty Wallace
 */
class ExceptionEvent extends Event {

	const EXCEPTION = 'exception';

	/** @var Exception */
	private $_exception;

	public function __construct(Exception $exception) {
		$this->_exception = $exception;
	}

	/**
	 * @return Exception
	 */
	public function getException() {
		return $this->_exception;
	}

}