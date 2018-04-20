<?php

namespace Tempest\Http;

use Tempest\Enums\Enum;

/**
 * HTTP methods.
 *
 * @author Marty Wallace
 */
class Method extends Enum {

	const GET = 'GET';
	const POST = 'POST';
	const PUT = 'PUT';
	const PATCH = 'PATCH';
	const DELETE = 'DELETE';
	const HEAD = 'HEAD';
	const OPTIONS = 'OPTIONS';

}