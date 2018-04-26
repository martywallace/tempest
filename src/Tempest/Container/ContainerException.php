<?php

namespace Tempest\Container;

use Tempest\Exception;
use Psr\Container\ContainerExceptionInterface;

class ContainerException extends Exception implements ContainerExceptionInterface {

	const SERVICE_ALREADY_EXISTS = 'A service named "%s" already exists in the container.';
	const SERVICE_ALREADY_INSTANTIATED = 'The service "%s" has already been instantiated.';

}