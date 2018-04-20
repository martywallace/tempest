<?php namespace Tempest\Http\Modes;

class ActionMode implements RouteMode {

	/** @var string */
	private $controller;

	/** @var string */
	private $method;

	/** @var mixed[] */
	private $options;

	public function __construct(string $controller, string $method, array $options = []) {
		$this->controller = $controller;
		$this->method = $method;
		$this->options = $options;
	}

	/**
	 * @return string
	 */
	public function getController(): string {
		return $this->controller;
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