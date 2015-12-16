<?php namespace Tempest\Http;

/**
 * Stores common HTTP status codes.
 *
 * @package Tempest\Http
 * @author Marty Wallace
 */
class Status {

	const OK = 200;
	const MOVED_PERMANENTLY = 301;
	const FOUND = 302;
	const NOT_FOUND = 404;
	const METHOD_NOT_ALLOWED = 405;
	const INTERNAL_SERVER_ERROR = 500;
	const SERVICE_UNAVAILABLE = 503;

	public static function isInformational($value) {
		return $value >= 100 && $value < 200;
	}

	public static function isSuccessful($value) {
		return $value >= 200 && $value < 300;
	}

	public static function isRedirection($value) {
		return $value >= 300 && $value < 400;
	}

	public static function isClientError($value) {
		return $value >= 400 && $value < 500;
	}

	public static function isServerError($value) {
		return $value >= 500;
	}

}