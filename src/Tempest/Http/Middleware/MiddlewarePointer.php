<?php

namespace Tempest\Http\Middleware;

class MiddlewarePointer {

	/** @var string */
	private $middleware;

	/** @var string */
	private $method;

	/** @var mixed[] */
	private $options;

	public function __construct(string $middleware, string $method, array $options) {
		$this->middleware = $middleware;
		$this->method = $method;
		$this->options = $options;
	}

	/**
	 * @return string
	 */
	public function getMiddleware(): string {
		return $this->middleware;
	}

	/**
	 * @return string
	 */
	public function getMethod(): string {
		return $this->method;
	}

	/**
	 * @return mixed[]
	 */
	public function getOptions(): array {
		return $this->options;
	}

}