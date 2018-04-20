<?php namespace Tempest\Http\Modes;

final class RedirectMode implements RouteMode {

	/** @var string */
	private $location;

	/** @var bool */
	private $permanent;

	public function __construct(string $location, bool $permanent = false) {
		$this->location = $location;
		$this->permanent = $permanent;
	}

	/**
	 * @return string
	 */
	public function getLocation(): string {
		return $this->location;
	}

	/**
	 * @return bool
	 */
	public function getPermanent(): bool {
		return $this->permanent;
	}

}