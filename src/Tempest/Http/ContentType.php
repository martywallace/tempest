<?php namespace Tempest\Http;

use Tempest\Enums\Enum;

/**
 * Common HTTP content-types.
 *
 * @author Marty Wallace
 */
class ContentType extends Enum {

	const ANY = '*/*';
	const APPLICATION_X_WWW_FORM_URLENCODED = 'application/x-www-form-urlencoded';
	const APPLICATION_JAVASCRIPT = 'application/javascript';
	const APPLICATION_JSON = 'application/json';
	const APPLICATION_OCTET_STREAM = 'application/octet-stream';
	const APPLICATION_PDF = 'application/pdf';
	const APPLICATION_RAR = 'application/x-rar-compressed';
	const APPLICATION_RTF = 'application/rtf';
	const APPLICATION_TYPESCRIPT = 'application/typescript';
	const APPLICATION_X_7Z_COMPRESSED = 'application/x-7z-compressed';
	const APPLICATION_X_SHOCKWAVE_FLASH = 'application/x-shockwave-flash';
	const APPLICATION_X_TAR = 'application/x-tar';
	const APPLICATION_XML = 'application/xml';
	const APPLICATION_ZIP = 'application/zip';
	const AUDIO_AAC = 'audio/aac';
	const AUDIO_WEBB = 'audio/webm';
	const AUDIO_X_WAV = 'audio/x-wav';
	const FONT_OTF = 'font/otf';
	const FONT_TTF = 'font/ttf';
	const FONT_WOFF = 'font/woff';
	const FONT_WOFF2 = 'font/woff2';
	const IMAGE_GIF = 'image/gif';
	const IMAGE_JPEG = 'image/jpeg';
	const IMAGE_PNG = 'image/png';
	const IMAGE_SVG = 'image/svg+xml';
	const IMAGE_TIFF = 'image/tiff';
	const IMAGE_X_ICON = 'image/x-icon';
	const MULTIPART_FORM_DATA = 'multipart/form-data';
	const TEXT_CSS = 'text/css';
	const TEXT_CSV = 'text/csv';
	const TEXT_HTML = 'text/html';
	const TEXT_PLAIN = 'text/plain';
	const VIDEO_MPEG = 'video/mpeg';
	const VIDEO_WEBM = 'video/webm';

}