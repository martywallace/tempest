<?php namespace Tempest\Http;

use Tempest\Enums\Enum;

/**
 * Stores common HTTP status codes and provides some utility methods related to status codes.
 *
 * @author Marty Wallace
 */
class Status extends Enum {

	const OK = 200;
	const CREATED = 201;
	const ACCEPTED = 202;
	const NON_AUTHORITATIVE_INFORMATION = 203;
	const NO_CONTENT = 204;
	const RESET_CONTENT = 205;
	const PARTIAL_CONTENT = 206;

	const MULTIPLE_CHOICES = 300;
	const MOVED_PERMANENTLY = 301;
	const FOUND = 302;
	const SEE_OTHER = 303;
	const NOT_MODIFIED = 304;
	const USE_PROXY = 305;
	const SWITCH_PROXY = 306;
	const TEMPORARY_REDIRECT = 307;
	const PERMANENT_REDIRECT = 308;

	const BAD_REQUEST = 400;
	const UNAUTHORIZED = 401;
	const FORBIDDEN = 403;
	const NOT_FOUND = 404;
	const METHOD_NOT_ALLOWED = 405;
	const NOT_ACCEPTABLE = 406;
	const PROXY_AUTHENTICATION_REQUIRED = 407;
	const REQUEST_TIMEOUT = 408;
	const CONFLICT = 409;
	const GONE = 410;
	const LENGTH_REQUIRED = 411;
	const PRECONDITION_FAILED = 412;
	const REQUEST_ENTITY_TOO_LARGE = 413;
	const REQUEST_RI_TOO_LONG = 414;
	const UNSUPPORTED_MEDIA_TYPE = 415;
	const REQUESTED_RANGE_NOT_SATISFIABLE = 416;
	const EXPECTATION_FAILED = 417;
	const UPGRADE_REQUIRED = 426;
	const PRECONDITION_REQUIRED = 428;
	const TOO_MANY_REQUESTS = 429;
	const REQUEST_HEADER_FIELDS_TOO_LARGE = 431;

	const INTERNAL_SERVER_ERROR = 500;
	const NOT_IMPLEMENTED = 501;
	const BAD_GATEWAY = 502;
	const SERVICE_UNAVAILABLE = 503;
	const GATEWAY_TIMEOUT = 504;
	const HTTP_VERSION_NOT_SUPPORTED = 505;
	const VARIANT_ALSO_NEGOTIATES = 506;
	const NOT_EXTENDED = 510;
	const NETWORK_AUTHENTICATION_REQUIRED = 511;

	/**
	 * Determine whether a status code is within the 1xx range.
	 *
	 * @param int $value The status code.
	 *
	 * @return bool
	 */
	public static function isInformational($value) {
		return $value >= 100 && $value < 200;
	}

	/**
	 * Determine whether a status code is within the 2xx range.
	 *
	 * @param int $value The status code.
	 *
	 * @return bool
	 */
	public static function isSuccessful($value) {
		return $value >= 200 && $value < 300;
	}

	/**
	 * Determine whether a status code is within the 3xx range.
	 *
	 * @param int $value The status code.
	 *
	 * @return bool
	 */
	public static function isRedirection($value) {
		return $value >= 300 && $value < 400;
	}

	/**
	 * Determine whether a status code is within the 4xx range.
	 *
	 * @param int $value The status code.
	 *
	 * @return bool
	 */
	public static function isClientError($value) {
		return $value >= 400 && $value < 500;
	}

	/**
	 * Determine whether a status code is within the 5xx range.
	 *
	 * @param int $value The status code.
	 *
	 * @return bool
	 */
	public static function isServerError($value) {
		return $value >= 500;
	}

}