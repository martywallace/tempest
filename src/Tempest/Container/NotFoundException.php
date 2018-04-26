<?php

namespace Tempest\Container;

use Psr\Container\NotFoundExceptionInterface;

class NotFoundException extends ContainerException implements NotFoundExceptionInterface {

	const SERVICE_NOT_FOUND = 'A service with the ID "%s" does not exist within the container.';

}