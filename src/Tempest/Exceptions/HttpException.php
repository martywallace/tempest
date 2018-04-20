<?php

namespace Tempest\Exceptions;

/**
 * An exception thrown by the Http kernel.
 *
 * @author Ascension Web Development
 */
class HttpException extends KernelException {

	const CONTROLLER_DOES_NOT_EXIST = 'Controller class "%s" does not exist.';
	const CONTROLLER_DOES_NOT_DEFINE_METHOD = 'Controller class "%s" does not contain a method "%s".';
	const MIDDLEWARE_DOES_NOT_EXIST = 'Middleware class "%s" does not exist.';
	const MIDDLEWARE_DOES_NOT_DEFINE_METHOD = 'Middleware class "%s" does not contain a method "%s".';
	const SESSION_DISABLED = 'Cannot start session - sessions are disabled.';
	const SESSION_ALREADY_STARTED = 'Cannot start session - there is already an active session.';
	const SESSION_COULD_NOT_ENABLE = 'Could not enable sessions.';
	const ROUTE_NO_MODE = 'Route "%s" does not have a mode set.';

}