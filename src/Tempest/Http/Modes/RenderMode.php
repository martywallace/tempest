<?php namespace Tempest\Http\Modes;

final class RenderMode implements RouteMode {

	/** @var string */
	private $template;

	/** @var mixed[] */
	private $context;

	public function __construct(string $template, array $context) {
		$this->template = $template;
		$this->context = $context;
	}

	/**
	 * @return string
	 */
	public function getTemplate(): string {
		return $this->template;
	}

	/**
	 * @return mixed[]
	 */
	public function getContext(): array {
		return $this->context;
	}

}