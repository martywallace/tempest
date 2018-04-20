<?php

namespace Tempest\Exceptions;

use Exception as BaseException;
use ReflectionClass;

/**
 * An exception thrown by Tempest.
 *
 * @author Ascension Web Development
 */
class Exception extends BaseException {

	/**
	 * The class name of the exception, not including its namespace as provided
	 * by get_class or ::class.
	 *
	 * @return string
	 */
	public function getName(): string {
		return (new ReflectionClass($this))->getShortName();
	}

}