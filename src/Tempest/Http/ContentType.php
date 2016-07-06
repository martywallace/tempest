<?php namespace Tempest\Http;

use Tempest\Utils\Enum;
use Tempest\Utils\StringUtil;

/**
 * Stores common content-types.
 *
 * @package Tempest\Http
 * @author Marty Wallace
 */
class ContentType extends Enum {

	const APPLICATION_X_WWW_FORM_URLENCODED = 'application/x-www-form-urlencoded';
	const APPLICATION_JSON = 'application/json';
	const MULTIPART_FORM_DATA = 'multipart/form-data';
	const TEXT_PLAIN = 'text/plain';

	/**
	 * Determine whether an input content-type string matches another. Both values are
	 * {@link StringUtil::slugify slugified} to allow more forgiving results. Any additional information (e.g. charset)
	 * is removed from both strings before comparison as well.
	 *
	 * @param string $input The input content-type.
	 * @param string $compare The content-type to compare the input against.
	 *
	 * @return bool
	 */
	public static function matches($input, $compare) {
		$input = preg_replace('/;.*$/', '', $input);
		$compare = preg_replace('/;.*$/', '', $compare);

		return StringUtil::slugify($input) === StringUtil::slugify($compare);
	}

}