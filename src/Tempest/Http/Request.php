<?php namespace Tempest\Http;

final class Request {

	/**
	 * Capture an incoming HTTP request and generate a new {@link Request request} from it.
	 *
	 * @return static
	 */
	public static function capture() {
		return new static();
	}

	public function __construct() {
		//
	}

}