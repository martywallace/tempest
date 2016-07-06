<?php namespace Tempest\Utils;

use Exception;

/**
 * A very thin wrapper around the JSON encoding and decoding functions provided by PHP.
 *
 * @package Tempest\Utils
 * @author Marty Wallace
 */
class JSONUtil {

	/**
	 * Behaves the same as {@link json_encode()} unless {@link php_error_info()} returns anything other than
	 * {@link JSON_ERROR_NONE}, in which case an exception is thrown.
	 *
	 * @param mixed $json
	 * @param int $options
	 * @param int $depth
	 *
	 * @return string
	 *
	 * @throws Exception If an error occurred while attempting to encode the data.
	 */
	public static function encode($json, $options = 0, $depth = 512) {
		$string = json_encode($json);
		$error = json_last_error();

		if ($error !== JSON_ERROR_NONE) {
			throw new Exception(self::getErrorDescription($error));
		}

		return $string;
	}

	/**
	 * Behaves the same as {@link json_decode()} unless {@link php_error_info()} returns anything other than
	 * {@link JSON_ERROR_NONE}, in which case an exception is thrown.
	 *
	 * @param string $string
	 * @param bool $assoc
	 * @param int $depth
	 * @param int $options
	 *
	 * @return mixed
	 *
	 * @throws Exception If an error occurred while attempting to decode the data.
	 */
	public static function decode($string, $assoc = false, $depth = 512, $options = 0) {
		if (is_string($string)) {
			$json = json_decode($string, $assoc, $depth, $options);
			$error = json_last_error();

			if ($error !== JSON_ERROR_NONE) {
				throw new Exception(self::getErrorDescription($error));
			}

			return $json;
		} else {
			throw new Exception('JSONUtil::decode() expects a string.');
		}
	}

	/**
	 * Provide a description of a result from {@link json_last_error()}.
	 *
	 * @param int $error The error code.
	 *
	 * @return string
	 */
	public static function getErrorDescription($error) {
		switch ($error) {
			default: return ''; break;

			case JSON_ERROR_NONE: return 'No error has occurred.'; break;
			case JSON_ERROR_DEPTH: return 'The maximum stack depth has been exceeded.'; break;
			case JSON_ERROR_STATE_MISMATCH: return 'Invalid or malformed JSON.'; break;
			case JSON_ERROR_CTRL_CHAR: return 'Control character error, possibly incorrectly encoded.'; break;
			case JSON_ERROR_SYNTAX: return 'Syntax error.'; break;
			case JSON_ERROR_UTF8: return 'Malformed UTF-8 characters, possibly incorrectly encoded.'; break;
			case JSON_ERROR_RECURSION: return 'One or more recursive references in the value to be encoded.'; break;
			case JSON_ERROR_INF_OR_NAN: return 'One or more NAN or INF values in the value to be encoded.'; break;
			case JSON_ERROR_UNSUPPORTED_TYPE: return 'A value of a type that cannot be encoded was given.'; break;
		}
	}

}